<?php

namespace Vulcan\Rivescript\Triggers;

use Vulcan\Rivescript\Contracts\Trigger;

class Atomic implements Trigger
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
