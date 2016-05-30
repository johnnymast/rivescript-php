<?php

namespace Vulcan\Rivescript;

use SplFileObject;

class Parser extends Utility
{
    protected $tree = [
        'begin' => [
            'global' => [],
            'var'    => [],
            'sub'    => [],
            'person' => [],
            'array'  => []
        ],
        'topics'  => [],
        'objects' => []
    ];

    protected $trigger;

    protected $topic = 'random';

    protected $commands = [
        'Topic',
        'Trigger',
        'Response'
    ];

    /**
     * Process Rivescript file.
     *
     * @param string  $file
     */
    public function process($file)
    {
        $file       = new SplFileObject($file);
        $lineNumber = 1;

        while (! $file->eof()) {
            $currentCommand = null;
            $line           = new Line($file->fgets(), $lineNumber++);

            if ($line->isInterrupted() or $line->isComment()) continue;

            foreach ($this->commands as $type) {
                $command = $this->{'command'.$type}($line, $currentCommand);

                if (isset($command)) {
                    $currentCommand = $command;
                    continue 2;
                }
            }
        }

        $file = null;

        return $this->tree;
    }

    protected function commandTopic($line, $command)
    {
        if ($line->command() === '>') {
            list($command, $topic) = explode(' ', $line->value());

            $this->topic = $topic;
        }

        if ($line->command() === '<') {
            $this->topic = 'random';
        }
    }

    protected function commandTrigger($line, $command)
    {
        if ($line->command() === '+') {
            if (! isset($this->tree['topics']['random'])) {
                $this->tree['topics']['random'] = [
                    'includes' => [],
                    'inherits' => [],
                    'triggers' => []
                ];
            }

            $this->trigger = [
                'trigger'   => $line->value(),
                'reply'     => [],
                'condition' => [],
                'redirect'  => null,
                'previous'  => null,
            ];

            $this->tree['topics'][$this->topic]['triggers'][] = $this->trigger;

            $this->trigger['key'] = max(array_keys($this->tree['topics'][$this->topic]['triggers']));

            return null;
        }

        return $command;
    }

    protected function commandResponse($line, $command)
    {
        if ($line->command() === '-') {
            $this->trigger['reply'][] = $line->value();

            $this->tree['topics'][$this->topic]['triggers'][$this->trigger['key']] = $this->trigger;

            return null;
        }

        return $command;
    }
}
