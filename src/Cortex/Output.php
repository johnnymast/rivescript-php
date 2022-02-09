<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem;
use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Traits\Tags;

/**
 * Output class
 *
 * This class is responsible for generating the
 * bot response.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Output
{

    use Regex;
    use Tags;

    /**
     * The output string
     *
     * @var string
     */
    protected string $output = '';

    /**
     * Keep track of the recursion
     * in the output.
     *
     * @var int
     */
    protected int $recursion = 0;

    /**
     * Process the correct output response by the interpreter.
     *
     * @return string
     */
    public function process(): string
    {
        $triggers = synapse()->brain->topic()->triggers();
        $begin = synapse()->brain->topic("__begin__");

        $this->output = "";


        /**
         * 1. Check if topic is valid
         * 2. Process the triggers
         * 3. if valid trigger
         *   - Check if response is redirect
         *   -
         */
        if ($begin) {
            synapse()->rivescript->say("Begin label found. Starting processing.");

            $request = $begin->triggers()->get("request");

            if ($request) {
                $this->output = $request['responses']->process();

                if ($begin->isOk() === false) {
                    return $this->output;
                }
            }
        }


        return $this->processTopic();

        return trim($this->output);
    }

    protected function processTopic(): string
    {
        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $triggers = synapse()->brain->topic($topic)->triggers();

        foreach ($triggers as $trigger => $data) {
            $valid = $this->isValidTrigger($trigger);

            if ($valid === true) {
                synapse()->rivescript->say("Found trigger {$trigger}...");
                synapse()->memory->shortTerm()->put('trigger', $trigger);

                $response = $this->getValidResponse($trigger);

                if ($response) {
                    if ($response->isChangingTopic() === true) {
                        // validate target or continue
                        synapse()->rivescript->warn("Topic change detected.");
                        break;
                    } else {
                        $output = $response->getValue();

                        if ($output) {
                            $this->output .= $output;
                        }
                    }
                } else {
                    synapse()->rivescript->warn("Could not find a valid response for trigger \":trigger\"", [
                        "trigger" => $trigger
                    ]);
                }
            } else {
                synapse()->rivescript->warn("Could not find a valid trigger \":trigger\"", [
                    "trigger" => $trigger
                ]);

            }
        }

        return trim($this->output);
    }


    /**
     * Search through available triggers to find a possible match.
     *
     * @param string $trigger The trigger to find responses for.
     *
     * @return bool
     */
    protected function isValidTrigger(string $trigger): bool
    {
        $triggers = synapse()->triggers;
        foreach ($triggers as $class) {
            $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\{$class}";
            $triggerInstance = new $triggerClass(synapse()->input);

            $found = $triggerInstance->parse($trigger, synapse()->input);

            if ($found) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search through available triggers to find a possible match.
     *
     * @param string $trigger The trigger to find responses for.
     *
     * @return void
     */
    protected function searchTriggers2(string $trigger): void
    {
        synapse()->triggers->each(
            function ($class) use ($trigger) {
                $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\$class";
                $triggerInstance = new $triggerClass(synapse()->input);

                $found = $triggerInstance->parse($trigger, synapse()->input);

                if ($found === true) {
                    synapse()->rivescript->say("Found trigger {$trigger} van {$triggerClass}...");
                    synapse()->memory->shortTerm()->put('trigger', $trigger);
                    $this->output .= $this->getResponse($trigger);
                    return false;
                }
            }
        );
    }


    protected function getValidResponse(string $trigger)
    {
        $originalTrigger = synapse()->brain->topic()->triggers()->get($trigger);
        $queueItem = null;

        if ($originalTrigger['responses']) {
            $queueItem = $originalTrigger['responses']->process();

            if ($queueItem) {
                $queueItem->parse();
            }
        }

        return $queueItem;
    }

    /**
     * Fetch a response from the found trigger.
     *
     * @param string $trigger The trigger to get a response for.
     *
     * @return string
     */
    protected function getResponse(string $trigger)
    {

        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $originalTrigger = synapse()->brain->topic($topic)->triggers()->get($trigger);


//        // FIXME: Temp fix for rsts
//        if (isset($originalTrigger['responses']) === false) {
//            synapse()->rivescript->say("No response found.");
//            $this->output = "";
//            return $this->output;
//        }

        /**
         * Get the best suitable response from
         * the ResponseQueue.
         */
        $queueItem = $originalTrigger['responses']->process();
        // $queueItem = $this->parseResponse($response);

        /**
         * It could be possible that tags have altered the trigger.
         * If so evaluate possible changes.
         */
//
        $processedTrigger = synapse()->brain->topic()->triggers()->get($trigger, null);
//        $processedTopic = synapse()->memory->shortTerm()->get('topic') ?? 'random';

//        if ($topic !== $processedTopic) {
//            synapse()->rivescript->warn("topic changed from :old to :new.", [
//                "old" => $topic,
//                "new" => synapse()->memory->shortTerm()->get('topic'),
//            ]);
//        }
//
//        if (isset($processedTrigger['redirect'])) {
//            $valid = $this->isValidTrigger($processedTrigger['redirect']);
//
//
//            if ($valid === true) {
//                synapse()->rivescript->warn("Searching for trigger :trigger in topic :topic", ['trigger' => $processedTrigger["redirect"], 'topic' => $topic]);
//
//                $input = new Input($processedTrigger["redirect"], "local-user");
//                synapse()->input = $input;
//
//                return $this->searchTriggers2($processedTrigger['redirect']);
//            } else {
//                // FIXME: Tiggers wild wildcards end up in here. We need to fix this.
//
//                $trigger = $processedTrigger['redirect'];
//
////                if ($trigger == 'set test name test' || $trigger == 'test x') {
////                    return $output;
////                }
//                synapse()->rivescript->warn("Topic :new was not found. Restoring topic :old", [
//                    "new" => $processedTrigger['redirect'],
//                    "old" => synapse()->memory->shortTerm()->get('topic'),
//                ]);
//
//                synapse()->input = new Input($trigger, "local-user");
//
////                $newTrigger = synapse()->memory->shortTerm()->get('trigger');
//
//
//                print_r('Trigger -->' . $trigger . "<\n");
////                print_r('newTrigger -->' . $newTrigger . "<\n");
//                synapse()->memory->shortTerm()->put("topic", null);
//
//                return '@'.$this->processTopic( null );
//           //     return $this->getResponse($trigger);
//            }
//        }
//
//        return $output;
    }
}
