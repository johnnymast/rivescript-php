<?php

namespace Vulcan\Rivescript\Commands;

use Vulcan\Rivescript\Contracts\Command;

class Trigger implements Command
{
    public function parse($tree, $line, $command)
    {
        if ($line->command() === '-') {
            $this->trigger['reply'][] = $line->value();

            $this->tree['topics'][$this->topic]['triggers'][$this->trigger['key']] = $this->trigger;

            return null;
        }

        return [
            'command' => $command,
            'tree'    => $tree
        ];
    }
}
