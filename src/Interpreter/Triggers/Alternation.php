<?php

namespace Vulcan\Rivescript\Interpreter\Triggers;

class Alternation
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
