<?php

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Contracts\Command;

class VariableGlobal implements Command
{
    /**
     * Parse the command.
     *
     * @param Node   $node
     * @param string $command
     *
     * @return array
     */
    public function parse($node, $command)
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
