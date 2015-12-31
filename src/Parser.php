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

    protected $commands = [
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

            echo $line->number().': <b>'.$line->command().'</b> '.$line->value().'<br>';

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

            $trigger = [
                'trigger' => $line->value(),
                'reply'   => [],
                'condition' => [],
                'redirect' => null,
                'previous' => null,
            ];

            $this->tree['topics']['random']['triggers'][] = $trigger;
        }

        return $command;
    }

    protected function commandResponse($line, $command)
    {
        return $command;
    }
}
