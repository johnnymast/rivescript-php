<?php

/**
 * This class parses the array variable command type.
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
 * Class VariableArray
 */
class VariableArray implements Command
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

            if ($type === 'array') {
                $value = str_replace('array', '', $node->value());
                list($key, $value) = explode('=', $value);

                $key = trim($key);
                $value = trim($value);

                $value = explode(strpos($value, '|') ? '|' : ' ', $value);

                synapse()->memory->arrays()->put($key, $value);
            }
        }
    }
}
