<?php

/**
 * Output is responsible for outputting a reply.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Cortex
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex;

/**
 * The Output class.
 */
class Output
{

    /**
     * Information of where the information came from.
     *
     * @var Input
     */
    protected $input;

    /**
     * The output string
     *
     * @var string
     */
    protected $output = 'Error: Response could not be determined.';

    /**
     * Create a new Output instance.
     *
     * @param  Input  $input
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
     * @param  string  $trigger  The trigger to find responses for.
     *
     * @return void
     */
    protected function searchTriggers(string $trigger)
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
     * Fetch a response from the found trigger.
     *
     * @param  string  $trigger  The trigger to get a response for.
     *
     * @return string
     */
    protected function getResponse(string $trigger): string
    {
        $originalTrigger = synapse()->brain->topic()->triggers()->get($trigger);


        // FIXME: Temp fix for rsts
        if (isset($originalTrigger['responses']) == false) {
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
            $output .= $this->getResponse($processedTrigger['redirect']);
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
     * Parse the response through the available tags.
     *
     * @param  string  $response
     *
     * @return string
     */
    protected function parseResponse(
        string $response
    ): string {
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
