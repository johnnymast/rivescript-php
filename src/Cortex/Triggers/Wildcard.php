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
 * Wildcard class
 *
 * The Wildcard class determines if a provided trigger
 * is a wildcard.
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
class Wildcard extends Trigger
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

        $wildcards = [
     //       '/@(\s+)/' => ".*?\b",
            '/_/' => '[^\s\d]+?',
            '/#/' => '\\d+?',
            '/\*/' => '.*?',
            '/<zerowidthstar>/' => '^\*$',

        ];


        /*
         * FIXME: This code is buggy and should not be in release. This code is now
         * FIXME: glue to make arrays been seen as wildcards.
         */
        $array_names = '';
        if (preg_match_all('/@(\w+)/', $trigger, $array_names)) {
            $array_names = $array_names[1];

            foreach ($array_names as $array_name) {
                if ($array = synapse()->memory->arrays()->get($array_name)) {
                    array_walk($array, 'preg_quote');
                    $array_str = implode('|', $array);

                    $parsedTrigger = str_replace("(@$array_name)", "($array_str)", $trigger);
                    $parsedTrigger = str_replace("@$array_name", "(?:$array_str)", $parsedTrigger);
                }
            }

            if (@preg_match_all('/' . $parsedTrigger . '/ui', $input->source(), $results)) {
                $replacement = $results[1][0];
                $pattern = "(@{$array_name})";

                $currentWildcards = synapse()->memory->shortTerm()->get("wildcards") ?? [];
                $currentWildcards [] = $replacement;

                $trigger = str_replace($pattern, $replacement, $trigger);

                synapse()->memory->shortTerm()->put("wildcards", $currentWildcards);
            }
        }

        foreach ($wildcards as $pattern => $replacement) {
            $parsedTrigger = preg_replace($pattern, '(' . $replacement . ')', $trigger);


            if ($parsedTrigger === $trigger) {
                continue;
            }

            if (@preg_match_all('/' . $parsedTrigger . '$/ui', $input->source(), $results)) {
                synapse()->rivescript->say("Wildcard trigger");
                array_shift($results);


                $currentWildcards = synapse()->memory->shortTerm()->get("wildcards");

                $flat = Collection::make($results)->flatten()->all();

                foreach ($flat as $wildcard) {
                    $currentWildcards[] = $wildcard;
                }

                synapse()->memory->shortTerm()->put("wildcards", $currentWildcards);

                return true;
            }
        }

        return false;
    }
}
