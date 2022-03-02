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
use Axiom\Rivescript\Cortex\Trigger as TriggerData;

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
        $this->output = "";

        /**
         * Leaving this function empty for maybe further expansion
         * later on.
         */
        return $this->processTopic();
    }

    protected function processTopic(int $recursion = 0): string
    {
        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $begin = synapse()->brain->topic("__begin__");

        $triggers = synapse()->brain->topic($topic)->triggers();

        if ($recursion === synapse()->memory->global()->get('depth')) {
            synapse()->rivescript->warn("Top many recursive calls to :func", ["func" => __CLASS__ . "::" . __FUNCTION__]);
            return "ERR: Deep Recursion Detected";
        }

        /**
         * 1. Check if topic is valid
         * 2. Process the triggers
         * 3. if valid trigger
         *   - Check if response is redirect
         *   -
         */
        if ($begin) {
            synapse()->rivescript->say("Begin label found. Starting processing.");

//            $request = $begin->triggers()->get("request");
            $request = null;

            // FIXME ugly code award make this global.
            foreach ($begin->triggers() as $trigger) {
                if ($trigger->getText() === "request") {
                    $request = $trigger;
                    break;
                }
            }

            if ($request) {
                $response = $request->getQueue()->process();
                $output = $response->getValue();

                if ($output !== '') {
                    $this->output .= $output;
                }

                if ($begin->isOk() === false) {
                    return $this->output;
                }
            }
        }

        foreach ($triggers as $index => $trigger) {
            $valid = $this->isValidTrigger($trigger);

            if ($valid === true) {
                synapse()->rivescript->debug("Found trigger \":trigger\".", [
                    'trigger' => $trigger->getText(),
                ]);

                synapse()->memory->shortTerm()->put('trigger', $trigger);
                $response = $this->getValidResponseForTriggerAtIndex($index);

                if ($response) {
                    if ($response->isTopicChanged() === true) {
                        synapse()->rivescript->debug(
                            "Topic changed from \":from\" to \":to\"",
                            [
                                "from" => $topic,
                                "to" => synapse()->memory->shortTerm()->get('topic') ?? 'random'
                            ]
                        );

                        synapse()->rivescript->debug(
                            "Parsed trigger :trigger",
                            [
                                "trigger" => $response->getValue(),
                            ]
                        );

                        //     synapse()->memory->shortTerm()->put('trigger', $response->getValue());

                        /**
                         * The topic has changed to something else during $this->getValidResponse($trigger).
                         * Call $this->>processTopic again to process the new topic.
                         *
                         * @note in this case the input can stay the same.
                         */
                        //    return $this->processTopic(++$recursion);
                    }

                    if ($response->isRedirect() === true) {
                        synapse()->rivescript->debug("Found redirect to \":redirect\" current topic is \":topic\".", [
                            "redirect" => $response->getRedirect(),
                            "topic" => $response->getTopic()
                        ]);

                        synapse()->input = new Input($response->getRedirect(), synapse()->rivescript->getClientId());
                        synapse()->memory->shortTerm()->put('trigger', $response->getRedirect());

                        return $this->processTopic(++$recursion);
                    }

                    $output = $response->getValue();

                    if ($output) {
                        $this->output .= $output;
                        break;
                    }
                } else {
                    synapse()->rivescript->warn("Could not find a valid response for trigger \":trigger\"", [
                        "trigger" => $trigger->getText()
                    ]);
                }
            }
        }

        return trim($this->output);
    }


    /**
     * Search through available triggers to find a possible match.
     *
     * @param TriggerData $trigger The trigger to find responses for.
     *
     * @return bool
     */
    protected function isValidTrigger(TriggerData $trigger): bool
    {
        $triggers = synapse()->triggers;
        foreach ($triggers as $class) {
            $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\{$class}";
            $triggerInstance = new $triggerClass(synapse()->input);

            $found = $triggerInstance->parse($trigger->getText(), synapse()->input);

            if ($found) {
                return true;
            }
        }

        return false;
    }

    protected function getValidResponseForTriggerAtIndex(int $index = 0): ?ResponseQueueItem
    {
        $originalTrigger = synapse()->brain->topic()->triggers()->get($index);
//        $originalTrigger = $trigger;
        $queueItem = null;

        if ($originalTrigger->hasResponses() === true) {
            $queueItem = $originalTrigger
                ->getQueue()
                ->process();

            if ($queueItem) {
                $queueItem->parse();
            }
        }

        return $queueItem;
    }
}
