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

/**
 * CommandValidator interface
 *
 * Description:
 *
 * This class will dictate the rules for all command types
 * to make sure they check the syntax for the commands in the
 * script all across the board.
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
interface CommandValidator
{
    /**
     * Check the command for syntax errors.
     *
     * @return bool
     */
    public function checkSyntax(): bool;
}