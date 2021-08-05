<?php

/**
 * The brain teaches itself about rive script files.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Cortex
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Exceptions\ParseException;
use SplFileObject;

/**
 * The Brain class.
 */
class Brain
{
    /**
     * A collection of topics.
     *
     * @var Branch
     */
    protected $topics;

    // protected $strict = false;

    /**
     * Create a new instance of Brain.
     */
    public function __construct()
    {
        $this->createTopic('random');
//        $this->createTopic('condition');
    }

    /**
     * Teach the brain contents of a new file source.
     *
     * @param  string  $file  The Rivescript file to read.
     *
     * @throws ParseException
     */
    public function teach(string $file)
    {
        $commands = synapse()->commands;
        $script = new SplFileObject($file);
        $lineNumber = 0;

        while (!$script->eof()) {
            $currentCommand = null;
            $line = $script->fgets();
            $node = new Node($line, $lineNumber++);

            if ($node->isInterrupted() or $node->isComment()) {
                continue;
            }

            $commands->each(function ($command) use ($node, $currentCommand) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Commands\\$command";
                $commandClass = new $class();

                $result = $commandClass->parse($node, $currentCommand);

                if (isset($result['command'])) {
                    $currentCommand = $result['command'];

                    return false;
                }
            });
        }
    }

    /**
     * Return a topic.
     *
     * @param  string|null  $name  The name of the topic to return.
     * @return mixed|null
     */
    public function topic(string $name = null)
    {
        if (is_null($name)) {
            $name = synapse()->memory->shortTerm()->get('topic') ?: 'random';
        }

        if (!isset($this->topics[$name])) {
            return null;
        }

        return $this->topics[$name];
    }

    /**
     * Create a new Topic.
     *
     * @param  string  $name  The name of the topic to create.
     * @return Topic
     */
    public function createTopic(string $name): Topic
    {
        $this->topics[$name] = new Topic($name);

        return $this->topics[$name];
    }
}
