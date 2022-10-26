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

use Axiom\Collections\Collection;
use Axiom\Rivescript\Exceptions\ParseException;
use Axiom\Rivescript\Rivescript;

/**
 * Brain class
 *
 * The Brain teaches the interpreter how to respond from
 * the user's input.
 *
 * PHP version 8.0 and higher.
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
    protected Collection $topics;

    /**
     * A collection of topics.
     *
     * @var array<CodeObject>
     */
    protected Collection $objects;

    /**
     * @var \Axiom\Rivescript\Rivescript
     */
    protected Rivescript $master;

    /**
     * Create a new instance of Brain.
     */
    public function __construct()
    {
        $this->topics = Collection::make([]);

        $this->createTopic('random');
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
     * @throws \Axiom\Rivescript\Exceptions\ParseException
     * @return void
     */
    public function teach($stream): void
    {
        if (is_resource($stream)) {
            $lineNumber = 0;

            rewind($stream);

            $collect = false;
            $collectFor = null;
            $collectedContent = '';

            while (!feof($stream)) {
                $line = fgets($stream);
                $node = new Node($line, $lineNumber++);
//                synapse()->memory->shortTerm()->put('trigger', null);

                $command = $node->getCommand();

                if ($command->isEmpty() === true || $command->isComment() === true) {
                    unset($node);
                    continue;
                }

                if ($node->getTag() === '>' && str_starts_with($node->getValue(), "object")) {
                    $collect = true;
                    $collectFor = $command;
                    continue;
                }

                if ($node->getTag() === '<' && str_starts_with($node->getValue(), "object")) {
                    //    $collectedContent .= $node->getOriginalSource();
                    $collectedContent = trim($collectedContent);
                    $collectFor->setContent($collectedContent);

                    $command = $collectFor;
                    $collect = false;
                    $collectFor = null;
                    $collectedContent = '';
                }

                if ($collect === true) {
                    $collectedContent .= $node->getOriginalSource();
                    continue;
                }

                if ($command->isSyntaxValid() === true) {
                    //echo get_class($command) . " -- Topic: " . $this->topic()->getName(), "\n";
                    $command->detect();

                } else {

                    /**
                     * I am not 100% sure yet on what to do in this case.
                     * For now, we will debug the syntax errors and continue
                     * on our way.
                     */
                    $errors = $command->getSyntaxErrors();

                    throw new ParseException(current($errors));
                }
            }


            /**
             * @deprecated
             */
            $this->topics->each(fn(Topic $topic) => $topic->sortTriggers($topic->triggers()));
        }
    }

    /**
     * Return a topic.
     *
     * @param string|null $name The name of the topic to return.
     *
     * @return Topic|null
     */
    public function topic(string $name = null): Topic|null
    {
        if (is_null($name)) {
            $name = synapse()->memory->shortTerm()->get('topic') ?: 'random';
        }

        if (!$this->topics->has($name) === true) {
            return null;
        }

        return $this->topics->get($name);
    }

    /**
     * Return a codeobject.
     *
     * @param string|null $name The name of the topic to return.
     *
     * @return mixed|null
     */
    public function codeObject(string $name = null): mixed
    {
        if (!isset($this->objects[$name])) {
            return null;
        }

        return $this->objects[$name];
    }

    /**
     * Return the defined topics.
     *
     * @return \Axiom\Collections\Collection
     */
    public function topics(): Collection
    {
        return $this->topics;
    }

    /**
     * Return all defined code objects.
     *
     * @return \Axiom\Rivescript\Cortex\CodeObject[]
     */
    public function codeObjects(): array
    {
        return $this->objects;
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

    /**
     * Create a code object.
     *
     * @param string $name     The name of the object.
     * @param string $language The name of the programming language.
     * @param string $code     The code for this code object.
     *
     * @return \Axiom\Rivescript\Cortex\CodeObject
     */
    public function createCodeObject(string $name, string $language, string $code): CodeObject
    {
        $this->objects[$name] = new CodeObject($name, $language, $code);

        return $this->objects[$name];
    }
}
