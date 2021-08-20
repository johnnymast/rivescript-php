<?php

/**
 * Handle the greater than condition.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Conditions;

use Axiom\Rivescript\Contracts\Condition as ConditionContract;

/**
 * Class GreaterThan
 */
class GreaterThan extends Condition implements ConditionContract
{

    /**
     * Handle conditions '>' also known as greater than.
     *
     * @param  string  $source
     *
     * @return false|string
     */
    public function parse(string $source)
    {
        $pattern = "/^([\S]+) (>) ([\S]+) =>(.*)$/";

        if ($this->matchesPattern($pattern, $source) === true) {
            $matches = $this->getMatchesFromPattern($pattern, $source);

            if (isset($matches[0]) === true and count($matches[0]) >= 2) {
                if ($matches[0][1] > $matches[0][3]) {
                    return trim($matches[0][4]);
                }
            }
        }

        return false;
    }
}
