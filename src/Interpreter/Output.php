<?php

namespace Vulcan\Rivescript\Interpreter;

use Vulcan\Collections\Collection;

class Output
{
    /**
     * @var Input
     */
    protected $input;

    /**
     * @var String
     */
    protected $output;

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
        });

        return $this->output;
    }

    protected function searchTriggers($trigger)
    {
        synapse()->triggers->each(function($class) use ($trigger) {
            $triggerClass = "\\Vulcan\\Rivescript\\Interpreter\\Triggers\\$class";
            $triggerClass = new $triggerClass;

            $found = $triggerClass->parse($trigger, $this->input);

            if ($found['match'] === true) return $this->getResponse($trigger);
        });

        return false;
    }

    protected function getResponse($trigger)
    {
        $trigger = synapse()->brain->topic()->triggers()->get($trigger);

        $key = array_rand($trigger['responses']);

        $this->output = $this->parseResponse($trigger['responses'][$key]);
    }

    protected function parseResponse($response)
    {
        synapse()->tags->each(function($tag) use (&$response) {
            $class = "\\Vulcan\\Rivescript\\Interpreter\\Tags\\$tag";
            $tagClass = new $class;

            $response = $tagClass->parse($response);
        });

        return $response;
    }
}
