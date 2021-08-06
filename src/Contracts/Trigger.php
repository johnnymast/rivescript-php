<?php

namespace Axiom\Rivescript\Contracts;

use Axiom\Rivescript\Cortex\Input;

interface Trigger
{
    /**
     * Parse the trigger.
     *
     * @param  string  $trigger  The trigger to parse.
     * @param  Input   $input    Input information.
     *
     * @return bool
     */
    public function parse(string $trigger, Input $input): bool;
}
