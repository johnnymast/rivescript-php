<?php

/**
 * This class parses the global variable command type.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Commands
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;

/**
 * Class VariableGlobal
 */
class VariableGlobal implements Command
{
    /**
     * Parse the command.
     *
     * @param  Node  $node  The node is a line from the Rivescript file.
     *
     * @return void
     */
    public function parse(Node $node)
    {
        if ($node->command() === '!') {
            $type = strtok($node->value(), ' ');

            if ($type === 'global') {
                $value = str_replace('global', '', $node->value());
                list($key, $value) = explode('=', $value);

                $key = trim($key);
                $value = trim($value);

                synapse()->memory->global()->put($key, $value);
            }
        }
    }
}
