<?php

namespace Vulcan\Rivescript\Interpreter\Triggers;

class Atomic
{
    public function parse($trigger, $message)
    {
        if ($trigger === $message) {
            return true;
        }

        return false;
    }
}
