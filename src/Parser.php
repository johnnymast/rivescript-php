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
            'array'  => [],
        ],
        'topics'   => [],
        'objects'  => [],
        'metadata' => [
            'topic'   => 'random',
            'trigger' => null,
        ],
    ];

    protected $trigger;

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

            foreach ($this->commands as $class) {
                $class        = "\Vulcan\Rivescript\Commands\\$class";
                $commandClass = new $class;

                $result = $commandClass->parse($this->tree, $line, $currentCommand);

                if (isset($result['command'])) {
                    $currentCommand = $result['command'];
                    continue 2;
                }

                $this->tree = $result['tree'];
            }
        }

        $file = null;

        return $this->tree;
    }
}
