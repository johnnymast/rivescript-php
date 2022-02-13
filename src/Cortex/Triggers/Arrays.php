<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Input;

/**
 * Arrays class
 *
 * The Atomic class determines if a provided trigger
 * is an array.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Triggers
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Arrays extends Trigger
{
    /**
     * Parse the trigger.
     *
     * @param string $trigger The trigger to parse.
     * @param Input  $input   Input information.
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

            if (@preg_match_all('/' . $trigger . '/ui', $input->source(), $wildcards)) {
                array_shift($wildcards);

                if ($wildcards) {
                    $wildcards = Collection::make($wildcards)->flatten()->all();

                    synapse()->memory->shortTerm()->put('wildcards', $wildcards);
                }

                return true;
            }
        }

        return false;
    }
}
