<?php

namespace Vulcan\Rivescript\Cortex;

use Vulcan\Collections\Collection;

class Output
{
    /**
     * @var Array
     */
    protected $data;

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var String
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
     * Process the corrent output response by the interpreter.
     *
     * @return Mixed
     */
    public function process()
    {
        synapse()->brain->topic()->triggers()->each(function($data, $trigger) {
            $this->searchTriggers($trigger);

            if ($this->output !== 'Error: Response could not be determined.') return false;
        });

        return $this->output;
    }

    /**
     * Search through available triggers to find a possible match.
     *
     * @param  String  $trigger
     * @return Boolean
     */
    protected function searchTriggers($trigger)
    {
        synapse()->triggers->each(function($class) use ($trigger) {
            $triggerClass = "\\Vulcan\\Rivescript\\Cortex\\Triggers\\$class";
            $triggerClass = new $triggerClass;

            $found = $triggerClass->parse($trigger, $this->input);

            if ($found === true) {
                $this->getResponse($trigger);

                return false;
            }
        });
    }

    /**
     * Fetch a response from the found trigger.
     *
     * @param  String  $trigger;
     * @return void
     */
    protected function getResponse($trigger)
    {
        $trigger      = synapse()->brain->topic()->triggers()->get($trigger);
        $key          = array_rand($trigger['responses']);
        $this->output = $this->parseResponse($trigger['responses'][$key]);
    }

    /**
     * Parse the response through the available tags.
     *
     * @param  String  $response
     * @return String
     */
    protected function parseResponse($response)
    {
        synapse()->tags->each(function($tag) use (&$response) {
            $class = "\\Vulcan\\Rivescript\\Cortex\\Tags\\$tag";
            $tagClass = new $class;

            $response = $tagClass->parse($response, $this->data);
        });

        return $response;
    }
}
