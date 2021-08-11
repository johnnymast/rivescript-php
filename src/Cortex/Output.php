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
     * The data to input.
     *
     * @var array
     */
    protected $data;

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
                    $this->getResponse($trigger);

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
     * @return void
     */
    protected function getResponse(string $trigger)
    {
        $trigger = synapse()->brain->topic()->triggers()->get($trigger);

        if (isset($trigger['redirect'])) {
            return $this->getResponse($trigger['redirect']);
        }

        $responses = $trigger['responses']->process();
        $this->output = $this->parseResponse($responses);


        //synapse()->memory->replies()->push($this->output);

//        $key = array_rand($trigger['responses']);
//        $this->output = $this->parseResponse($trigger['responses'][$key]);
    }

    /**
     * Parse the response through the available tags.
     *
     * @param  string  $response
     *
     * @return string
     */
    protected function parseResponse(string $response): string
    {
        synapse()->tags->each(
            function ($tag) use (&$response) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagClass = new $class('response');

                echo "Response: {$response}\n";

                $response = $tagClass->parse($response, $this->input);
            }
        );

        return $response;
    }
}
