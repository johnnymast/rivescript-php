<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;

/**
 * VariableGlobal class
 *
 * This class handles the global variable command (! global)
 * and stores the definition in memory.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class VariableGlobal implements Command
{
    /**
     * Parse the command.
     *
     * @param Node $node The node is a line from the Rivescript file.
     *
     * @return void
     */
    public function parse(Node $node): void
    {
        if ($node->command() === '!') {
            $type = strtok($node->value(), ' ');

            if ($type === 'global') {
                $value = str_replace('global', '', $node->value());
                [$key, $value] = explode('=', $value);

                $key = trim($key);
                $value = trim($value);

                synapse()->memory->global()->put($key, $value);
            }
        }
    }
}
