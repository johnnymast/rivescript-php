<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\ResponseQueue;

/**
 * ResponseQueueItem class
 *
 * The ResponseQueueItem represents one response in the ResponseQueue.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\ResponseQueue
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ResponseQueueItem
{

    /**
     * The command prefix.
     *
     * @var string
     */
    public string $command = "";

    /**
     * The response string
     *
     * @var string
     */
    public string $value = "";

    /**
     * The original response string
     *
     * @var string
     */
    public string $original = "";

    /**
     * The response type.
     *
     * @var string
     */
    public string $type = 'atomic';

    /**
     * The sort order of the response.
     *
     * @var int
     */
    public int $order = 0;

    /**
     * Local parser options at this item.
     *
     * @var array<string, string>
     */
    public array $options = [];

    /**
     * Indicate the response changes topic.
     *
     * @var bool
     */
    private bool $topicIsChanged = false;

    /**
     * This is the trigger for this response.
     *
     * @var string
     */
    public string $triggerString = '';

    /**
     * This is the topic for this response.
     *
     * @var string
     */
    public string $triggerTopic = '';

    /**
     * Set a redirect target.
     *
     * @var string
     */
    protected string $redirect = '';

    /**
     * ResponseQueueItem constructor.
     *
     * @param string               $command The command prefix.
     * @param string               $value   The command value.
     * @param string               $type    The type of response.
     * @param array<string,string> $options The local interpreter options.
     */
    public function __construct(string $command, string $value, string $type, array $trigger = [], array $options = [])
    {
        $this->command = $command;
        $this->value = $this->original = $value;
        $this->type = $type;
        $this->options = $options;
        $this->triggerString = $trigger['value'];
        $this->triggerTopic = $trigger['topic'];
    }

    /**
     * Return the command string.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * Return the command string.
     *
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Return the trigger.
     *
     * @return array
     */
    public function getTrigger(): ?array
    {
        return synapse()->brain->topic($this->triggerTopic)->triggers()->get($this->triggerString);
    }

    /**
     * Return the order of this queue item.
     *
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    public function isRedirect(): bool
    {
        return ($this->getRedirect() !== '');
    }

    public function isTopicChanged(): bool
    {
        return $this->topicIsChanged;
    }

    private function reset(): void
    {
        $this->source = $this->original;
        $this->topicIsChanged = false;
    }


    /**
     * Parse the response through the available Tags.
     *
     * @return string
     */
    public function parse(): string
    {
        // $this->reset();

        $this->topicIsChanged = false;

        foreach (synapse()->tags as $tag) {
            $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\{$tag}";
            $instance = new $class();

            $this->value = $instance->parse($this->value, synapse()->input);
        }

        if ($this->triggerTopic !== $this->getTopic()) {
            $this->topicIsChanged = true;
        }

        if ($this->command === '@') {
            $this->setRedirect($this->value);
        }

        return $this->value;
    }

    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function getRedirect(): string
    {
        return $this->redirect;
    }

    public function getTopic(): string
    {
        return synapse()->memory->shortTerm()->get('topic') ?? "random";
    }
}
