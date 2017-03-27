<?php

namespace Vulcan\Rivescript\Interpreter\Triggers;

class Atomic
{
    /**
     * Parse the trigger.
     *
     * @param  integer  $key
     * @param  string  $trigger
     * @param  string  $message
     * @return array
     */
    public function parse($trigger, $input)
    {
        if ($trigger === $input->source()) {
            return [
                'match' => true,
                'data'  => [],
            ];
        }

        return ['match' => false];
    }
}
