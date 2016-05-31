<?php

namespace Vulcan\Rivescript\Commands;

use Vulcan\Rivescript\Contracts\Command;

class Trigger implements Command
{
    public function parse($tree, $line, $command)
    {
        if ($line->command() === '+') {
            $currentTopic = $tree['metadata']['topic'];

            $trigger = [
                'trigger'   => $line->value(),
                'reply'     => [],
                'condition' => [],
                'redirect'  => null,
                'previous'  => null,
            ];

            if (! isset($tree['topics']['random'])) {
                $tree['topics']['random'] = [
                    'includes' => [],
                    'inherits' => [],
                    'triggers' => []
                ];
            }

            $tree['topics'][$currentTopic]['triggers'][]            = $trigger;
            $key                                                    = max(array_keys($tree['topics'][$currentTopic]['triggers']));
            $tree['topics'][$currentTopic]['triggers'][$key]['key'] = $key;

            return ['tree' => $tree];
        }

        return [
            'command' => $command,
            'tree'    => $tree
        ];
    }
}
