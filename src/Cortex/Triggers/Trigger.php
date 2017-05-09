<?php

namespace Vulcan\Rivescript\Cortex\Triggers;

use Vulcan\Rivescript\Contracts\Trigger as TriggerContract;

abstract class Trigger implements TriggerContract
{
    public function triggerFound()
    {
        return true;
    }

    public function triggerNotFound()
    {
        return false;
    }

    /**
     * Parse the response through the available tags.
     *
     * @param string $response
     *
     * @return string
     */
    protected function parseTags($trigger, $input)
    {
        synapse()->tags->each(function ($tag) use (&$trigger, $input) {
            $class = "\\Vulcan\\Rivescript\\Cortex\\Tags\\$tag";
            $tagClass = new $class('trigger');

            $trigger = $tagClass->parse($trigger, $input);
        });

        return mb_strtolower($trigger);
    }
}
