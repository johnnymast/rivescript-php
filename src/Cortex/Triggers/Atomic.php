<?php

/**
 * This class will parse the Atomic Trigger.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Triggers
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Rivescript\Cortex\Input;

/**
 * Atomic class
 */
class Atomic extends Trigger
{
    /**
     * Parse the trigger.
     *
     * @param  string  $trigger  The trigger to parse.
     * @param  Input   $input    Input information.
     *
     * @return bool
     */
    public function parse(string $trigger, Input $input): bool
    {
        $trigger = $this->parseTags($trigger, $input);

        if ($trigger === $input->source()) {
            return true;
        }

        return false;
    }
}
