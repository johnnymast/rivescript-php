<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Rivescript;

/**
 * Brain class
 *
 * The Brain teaches the interpreter how to respond from
 * the user's input.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Brain
{
    /**
     * A collection of topics.
     *
     * @var array<Topic>
     */
    protected array $topics;

    /**
     * @var \Axiom\Rivescript\Rivescript
     */
    protected Rivescript $master;

    /**
     * Create a new instance of Brain.
     */
    public function __construct()
    {
        $this->createTopic('random');
    }

    /**
     * Log a message to say.
     *
     * @param string $message
     *
     * @return void
     */
    public function say(string $message): void
    {
        echo "SAY: {$message}\n";
    }

    /**
     * Log a warning message.
     *
     * @param string $message
     *
     * @return void
     */
    public function warn(string $message): void
    {
        echo "WARNING: {$message}\n";
    }

    /**
     * Set the interpreter for future reference.
     *
     * @param \Axiom\Rivescript\Rivescript $master
     *
     * @return void
     */
    public function setMaster(Rivescript $master): void
    {
        $this->master = $master;
    }

    /**
     * Teach the Brain with new information.
     *
     * @param resource $stream the stream to read from.
     *
     * @return void
     */
    public function teach($stream): void
    {
        $commands = synapse()->commands;

        if (is_resource($stream)) {
            $lineNumber = 0;

            rewind($stream);

            while (!feof($stream)) {
                $line = fgets($stream);
                $node = new Node($line, $lineNumber++);
                //    echo "LINE: {$line}";

                if ($node->isInterrupted() === true || $node->isComment() === true || $node->isEmpty()) {
                    continue;
                }


                $commands->each(function ($command) use ($node) {
                    $class = "\\Axiom\\Rivescript\\Cortex\\Commands\\$command";
                    $commandClass = new $class();
                    $commandClass->parse($node);
                });
            }
        } else {
            echo "CANNOT TEACH INVALID RESOURCE STREAM\n";
        }
    }


//    public function getReply($user, $msg, $context, $step, $scope)
//    {
//
//        // Check if topics are sorted or not
//        $depth = 25;
//
//        $topic = synapse()->memory->shortTerm()->get('topic');
//
//        if (is_null($topic) === true || $topic == "undefined") {
//            $topic = "random";
//        }
//
//        if (!synapse()->brain->topic($topic)) {
//            $this->warn("User ${user} was in an empty topic named '${topic}");
//            $topic = "random";
//            synapse()->memory->shortTerm()->put('topic', $topic);
//        }
//
//
//        if ($step > $this->master->depth) {
//            return $this->master->errors["deepRecursion"];
//        }
//
//        // Are we in the BEGIN block?
////        if (context === "begin") {
////            topic = "__begin__";
////        }
//
//        // TODO: Initialize history here if it does exist.
//
//        // More topic sanity checking.
//        if (!synapse()->brain->topic($topic)) {
//            return "ERR: No default topic 'random' was found!";
//        }
//
//
//        $foundMatch = false;
//
//        /**
//         * Logitc for previous wich we didnt code yet.
//         */
//        if ($step == 0) {
//            $allTopics = [$topic];
//
////            if (self.master._topics[topic].includes || self.master._topics[topic].inherits) {
////                // Get ALL the topics!
////                allTopics = inherit_utils.getTopicTree(self.master, topic);
////            }
//
//            // Scan them all.
////            for ($j = 0; $j < count($allTopics); $j++) {
////                $top = $allTopics[$j];
////                $this->say("Checking topic ${top} for any %Previous's");
////
////
////            }
//        }
//
//        if ($foundMatch == false) {
//            $this->say("Searching their topic for a match...");
//        }
//    }


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
