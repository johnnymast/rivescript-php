<?php

namespace Vulcan\Rivescript\Commands;

use Vulcan\Rivescript\Contracts\Command;

class Response implements Command
{
    /**
     * Parse the command.
     *
     * @param  array  $tree
     * @param  object  $line
     * @param  string  $command
     * @return array
     */
    public function parse($tree, $line, $command)
    {
        if ($line->command() === '-') {
            $topic              = $tree['metadata']['topic'];
            $trigger            = $tree['metadata']['trigger'];
            $trigger['reply'][] = $line->value();

            $tree['topics'][$topic]['triggers'][$trigger['key']] = $trigger;

            return ['tree' => $tree];
        }

        return [
            'command' => $command,
            'tree'    => $tree
        ];
    }
}
