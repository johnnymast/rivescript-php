<?php

namespace Vulcan\Rivescript\Cortex\Commands;

use Vulcan\Rivescript\Contracts\Command;

class Trigger implements Command
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
        if ($node->command() === '+') {
            $topic = synapse()->memory->shortTerm()->get('topic') ?: 'random';

            synapse()->brain->topic($topic)->triggers()->put($node->value(), []);
            synapse()->memory->shortTerm()->put('trigger', $node->value());
        }
    }
}
