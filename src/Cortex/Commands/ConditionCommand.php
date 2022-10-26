<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Cortex\RegExpressions;

/**
 * ConditionCommand class
 *
 * Description:
 *
 * This handle and validate the command type "condition".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#CONDITION
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ConditionCommand extends Command
{
    /**
     * Check the syntax for conditions.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {

        if ($this->getNode()->getTag() == '*') {
            $value = $this->getNode()->getValue();

            if ($this->matchesPattern(RegExpressions::CONDITION_SYNTAX1, $value) === false) {
                $this->addSyntaxError(
                    "Invalid format for !ConditionCommand: should be like `* value symbol value => response`"
                );
            }
        }

        return $this->isSyntaxValid();
    }

    /**
     * Detect if the command is a condition.
     *
     * @return bool
     */
    public function detect(): bool
    {
        if ($this->getNode()->getTag() == '*') {
            return true;
        }
        return false;
    }
}
