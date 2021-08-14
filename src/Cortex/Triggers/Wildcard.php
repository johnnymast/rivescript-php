<?php

/**
 * This class will parse the Wildcard Trigger.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Triggers
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Input;

/**
 * Wildcard class
 */
class Wildcard extends Trigger
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

        $wildcards = [
            '/\*$/' => '.*?',
            '/\*/' => '\\w+?',
            '/#/' => '\\d+?',
            '/_/' => '[a-z]?',
            '/<zerowidthstar>/' => '^\*$',
        ];

        foreach ($wildcards as $pattern => $replacement) {
            $parsedTrigger = preg_replace($pattern, '('.$replacement.')', $trigger);

            if ($parsedTrigger == $trigger) {
                continue;
            }

            if (@preg_match_all('/'.$parsedTrigger.'$/u', $input->source(), $wildcards)) {
                array_shift($wildcards);

                $wildcards = Collection::make($wildcards)->flatten()->all();

                synapse()->memory->shortTerm()->put('wildcards', $wildcards);

                return true;
            }
        }

        return false;
    }
}
