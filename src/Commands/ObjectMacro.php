<?php

namespace Vulcan\Rivescript\Commands;

use Vulcan\Rivescript\Contracts\Command;

class ObjectMacro implements Command
{
    public function parse($tree, $line, $command)
    {
        if ($line->command() === '>') {
            if (count(explode(' ', $line->value()) === 3)) {
                list($type, $method, $language) = explode(' ', $line->value());

                if ($type === 'object' and $language === 'php') {
                    $tree['metadata']['object'] = [
                        'name'  => $method,
                        'code'  => [],
                    ];
                }
            }
        }

        else if ($line->command() === '<') {
            list($type) = explode(' ', $line->value());

            if ($type === 'object') {
                $name = $tree['metadata']['object']['name'];
                $code = $tree['metadata']['object']['code'];

                $tree['objects'][$name] = implode("\n", $code);

                $tree['metadata']['object'] = null;
            }
        }

        else if (! is_null($tree['metadata']['object'])) {
            $tree['metadata']['object']['code'][] = $line->value();
        }

        return [
            'command' => $command,
            'tree'    => $tree
        ];
    }
}
