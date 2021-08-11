<?php

/**
 * This class parses the person variable command type.
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
 * Class VariablePerson
 */
class VariablePerson implements Command
{
    /**
     * Parse the command.
     *
     * @param  Node  $node  The node is a line from the Rivescript file.
     *
     * @return void
     */
    public function parse($node)
    {
        if ($node->command() === '!') {
            $type = strtok($node->value(), ' ');

            if ($type === 'person') {
                $value = str_replace('person', '', $node->value());
                list($key, $value) = explode('=', $value);

                $key = trim($key);
              //  $key = '/\b'.preg_quote($key, '/').'\b/'; // Convert the "key" to a regular expression ready format
                $value = trim($value);

                synapse()->memory->person()->put($key, $value);
            }
        }
    }
}
