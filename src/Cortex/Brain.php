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
use Axiom\Rivescript\Rivescript;
use SplFileObject;

/**
 * The Brain class.
 */
class Brain
{
    /**
     * A collection of topics.
     *
     * @var array<Topic>
     */
    protected $topics;

    /**
     * Create a new instance of Brain.
     */
    public function __construct()
    {
        $this->createTopic('random');
    }

    /**
     * Teach the brain contents of a new information.
     *
     * @param resource $stream the stream to read from.
     *
     * @return void
     */
    public function teach($stream)
    {
        $commands = synapse()->commands;

        if (is_resource($stream)) {
            $lastNode = null;
            $lineNumber = 0;

            rewind($stream);
            while (!feof($stream)) {
                $line = fgets($stream);
                $node = new Node($line, $lineNumber++);

                echo "LINE: {$line}";

                if ($node->isInterrupted() or $node->isComment()) {
                    continue;
                }

                $commands->each(function ($command) use ($node) {
                    $class = "\\Axiom\\Rivescript\\Cortex\\Commands\\$command";
                    $commandClass = new $class();
                    $commandClass->parse($node);
                });
            }
        }
    }

    /**
     * Return a topic.
     *
     * @param string|null $name The name of the topic to return.
     *
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
     * @param string $name The name of the topic to create.
     *
     * @return Topic
     */
    public function createTopic(string $name): Topic
    {
        $this->topics[$name] = new Topic($name);

        return $this->topics[$name];
    }
}
