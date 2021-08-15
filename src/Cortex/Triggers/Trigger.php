<?php
/**
 * This is the base class for all the Triggers. It
 * provides help in translating tags in the Triggers.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Triggers
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Rivescript\Contracts\Trigger as TriggerContract;
use Axiom\Rivescript\Cortex\Input;

/**
 * The Trigger baseclass.
 */
abstract class Trigger implements TriggerContract
{

    /**
     * Parse the response through the available tags.
     *
     * @param  string  $trigger  The trigger to parse tags on.
     * @param  Input   $input    Input information.
     *
     * @return string
     */
    protected function parseTags(string $trigger, Input $input): string
    {
        synapse()->tags->each(function($tag) use (&$trigger, $input) {
            $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
            $tagClass = new $class('trigger');

            $trigger = $tagClass->parse($trigger, $input);
        });

        return mb_strtolower($trigger);
    }
}
