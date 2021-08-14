<?php

/**
 * This class will parse the Arrays Trigger.
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
 * Arrays class
 */
class Arrays extends Trigger
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

        if (preg_match_all('/@(\w+)/', $trigger, $array_names)) {
            $array_names = $array_names[1];

            foreach ($array_names as $array_name) {
                if ($array = synapse()->memory->arrays()->get($array_name)) {
                    array_walk($array, 'preg_quote');
                    $array_str = implode('|', $array);

                    $trigger = str_replace("(@$array_name)", "($array_str)", $trigger);
                    $trigger = str_replace("@$array_name", "(?:$array_str)", $trigger);
                }
            }

            if (@preg_match_all('/'.$trigger.'$/ui', $input->source(), $stars)) {
                array_shift($stars);

                if ($stars) {
                    $stars = Collection::make($stars)->flatten()->all();

               //     synapse()->memory->shortTerm()->put('wildcards', $stars);
                }

                return true;
            }
        }

        return false;
    }
}
