<?php

namespace Vulcan\Rivescript\Interpreter\Commands;

use Vulcan\Rivescript\Contracts\Command;

class VariableSubstitute implements Command
{
    /**
     * Parse the command.
     *
     * @param  Node  $node
     * @param  String  $command
     * @return array
     */
    public function parse($node, $command)
    {
        if ($node->command() === '!') {
            $type = strtok($node->value(), ' ');

            if ($type === 'sub') {
                $value             = str_replace('sub', '', $node->value());
                list($key, $value) = explode('=', $value);

                $key   = trim($key);
                $value = trim($value);

                synapse()->memory->substitute()->put($key, $value);
            }
        }
    }
}
