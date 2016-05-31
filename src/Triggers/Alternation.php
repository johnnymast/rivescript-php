<?php

namespace Vulcan\Rivescript\Triggers;

use Vulcan\Rivescript\Contracts\Trigger;

class Alternation implements Trigger
{
    public function parse($key, $trigger, $message)
    {
        if (@preg_match('/'.$trigger.'/', $message, $stars)) {

            array_shift($stars);

            return [
                'match' => true,
                'key'   => $key,
                'data'  => ['stars' => $stars],
            ];
        }

        return ['match' => false];
    }
}
