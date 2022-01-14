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
     * Information of where the information came from.
     *
     * @var Input
     */
    protected Input $input;

    /**
     * The output string
     *
     * @var string
     */
    protected string $output = 'Error: Response could not be determined.';

    /**
     * Keep track of the recursion
     * in the output.
     *
     * @var int
     */
    protected int $recursion = 0;

    /**
     * Create a new Output instance.
     *
     * @param Input $input
     */
    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    /**
     * Process the correct output response by the interpreter.
     *
     * @return string
     */
    public function process(): string
    {
        synapse()->brain->topic()->triggers()->each(
            function ($data, $trigger) {
                $this->searchTriggers($trigger);

                if ($this->output !== 'Error: Response could not be determined.') {
                    return false;
                }
            }
        );

        return $this->output;
    }

    /**
     * Search through available triggers to find a possible match.
     *
     * @param string $trigger The trigger to find responses for.
     *
     * @return void
     */
    protected function searchTriggers(string $trigger): void
    {
        synapse()->triggers->each(
            function ($class) use ($trigger) {
                $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\$class";
                $triggerClass = new $triggerClass($this->input);

                $found = $triggerClass->parse($trigger, $this->input);

                if ($found === true) {
                    synapse()->memory->shortTerm()->put('trigger', $trigger);
                    $this->output = $this->getResponse($trigger);
                    return false;
                }
            }
        );
    }

    /**
     * Process the correct output response by the interpreter.
     *
     * @return string
     * @deprecated
     */
    public function process2(): string
    {
        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $triggers = synapse()->brain->topic($topic)->triggers();
        $triggerClasses = synapse()->triggers;


        synapse()->brain->say("Analyzing topic {$topic}...");

        echo "======================================================\n";
        echo "PROCESS TOPIC: {$topic}\n";
        echo "======================================================\n";

        $this->output = 'Error: Response could not be determined.';

        if ($this->recursion == 25) {
            $this->recursion = 0;
            return $this->output;
        }

        $source = $this->input->source();

//        Checking topic random for any %Previous's
//No %Previous in this topic!
        synapse()->brain->say("Searching their topic for a match...");


        foreach ($triggers as $trigger => $info) {
            //  $isValid = $this->isValidTrigger($trigger);
            $this->searchTriggers($trigger);
        }
        return $this->output;
    }

    /**
     * Search through available triggers to find a possible match.
     *
     * @param string $trigger The trigger to find responses for.
     *
     * @return void
     * @deprecated
     */
    protected function searchTriggers2(string $trigger)
    {
        synapse()->triggers->each(
            function ($class) use ($trigger) {
                $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\$class";
                $triggerClass = new $triggerClass($this->input);

                $found = $triggerClass->parse($trigger, $this->input);

//                synapse()->brain->say("Try to match \"{$source}\" against {$trigger} ({$trigger})");
                if ($found === true) {
                    synapse()->memory->shortTerm()->put('trigger', $trigger);
                    $this->output = $this->getResponse($trigger);
                    return false;
                }
            }
        );
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
        /**
         * Get the connected trigger classes.
         */
        $classes = synapse()->triggers->all();

        foreach ($classes as $class) {
            $triggerClass = "\\Axiom\\Rivescript\\Cortex\\Triggers\\$class";
            $triggerClass = new $triggerClass($this->input);

            $found = $triggerClass->parse($trigger, $this->input);

            if ($found === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch a response from the found trigger.
     *
     * @param string $trigger The trigger to get a response for.
     *
     * @return string
     */
    protected function getResponse(string $trigger): string
    {
        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $originalTrigger = synapse()->brain->topic($topic)->triggers()->get($trigger);


        // FIXME: Temp fix for rsts
        if (isset($originalTrigger['responses']) === false) {
            $this->output = "Error: Response could not be determined.";
            return $this->output;
        }
        /**
         * Get the best suitable response from
         * the ResponseQueue.
         */
        $response = $originalTrigger['responses']->process();
        $output = $this->parseResponse($response);

        /**
         * It could be possible that tags have altered the trigger.
         * If so evaluate possible changes.
         */
        $processedTrigger = synapse()->brain->topic()->triggers()->get($trigger);

        if (isset($processedTrigger['redirect'])) {
            //$output .= $this->getResponse($processedTrigger['redirect']);
            synapse()->input = new Input($processedTrigger['redirect'], 0);
            $this->process();
        }

// TODO:
//        $key = array_rand($trigger['responses']);
//        $this->output = $this->parseResponse($trigger['responses'][$key]);
        return $output;

// TODO:
//        $key = array_rand($trigger['responses']);
//        $this->output = $this->parseResponse($trigger['responses'][$key]);
//        return $output;
    }


    /**
     * Fetch a response from the found trigger.
     *
     * @param string $trigger The trigger to get a response for.
     *
     * @return string
     */
    protected function getResponse2(string $trigger): string
    {
        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $originalTrigger = synapse()->brain->topic($topic)->triggers()->get($trigger);
        echo "GETRESPONSE TOPIC: {$topic}\n";
        echo "GETRESPONSE TRIGGER: {$trigger}\n";

        // FIXME: Temp fix for rsts
        if (isset($originalTrigger['responses']) === false) {
            return false;
        }

        /**
         * Get the best suitable response from
         * the ResponseQueue.
         */
        $response = $originalTrigger['responses']->process();

        $output = $this->parseResponse($response);

        /**
         * It could be possible that tags have altered the trigger.
         * If so evaluate possible changes.
         */
        $processedTrigger = synapse()->brain->topic()->triggers()->get($trigger);

        echo "GETRESPONSE CHECKING FOR REDIRECT ON: {$trigger}\n";

        if (isset($processedTrigger['redirect'])) {
//            synapse()->brain->say("Pretend user said: {$processedTrigger['redirect']}");
            synapse()->brain->say("Redirect to Trigger : {$processedTrigger['redirect']} Topic: {$topic}");
            synapse()->memory->shortTerm()->put('trigger', $processedTrigger['redirect']);

            //     return false;
//            $output .= $this->getResponse($processedTrigger['redirect']);
            $this->recursion++;
            $output .= $this->process();
        }

// TODO:
//        $key = array_rand($trigger['responses']);
//        $this->output = $this->parseResponse($trigger['responses'][$key]);

        $output = $this->parseTags($output);
        echo "GETRESPONSE OUTPUT: {$output}\n";
        return $output;

// TODO:
//        $key = array_rand($trigger['responses']);
//        $this->output = $this->parseResponse($trigger['responses'][$key]);
//        return $output;
    }

    /**
     * Parse the response through the available tags.
     *
     * @param string $response
     *
     * @return string
     */
    protected function parseResponse(string $response): string
    {
        synapse()->tags->each(
            function ($tag) use (&$response) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagClass = new $class();

                $response = $tagClass->parse($response, $this->input);
            }
        );

        return $response;
    }
}
