<?php

namespace Vulcan\Rivescript\Interpreter\Triggers;

class Atomic
{
    public function parse($key, $trigger, $message)
    {
        if ($trigger === $message) {
            return [
                'match' => true,
                'key'   => $key,
                'data'  => [],
            ];
        }

        return ['match' => false];
    }
}
