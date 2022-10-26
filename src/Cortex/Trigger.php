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
use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;

/**
 * TriggerCommand class
 *
 * This class contains information about
 * a TriggerCommand itself.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Trigger
{
    /**
     * The text for the trigger.
     *
     * @var string
     */
    public string $text = '';

    /**
     * The topic the TriggerCommand belongs in.
     *
     * @var string
     */
    public string $topic = '';

    /**
     * The type of trigger atom/weighted etc.
     *
     * @var string
     */
    public string $type = '';

    /**
     * The weight of the TriggerCommand.
     *
     * @var int
     */
    public int $order = 0;

    /**
     * A reference to the ResponseCommand queue.
     *
     * @var \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue
     */
    public ResponseQueue $queue;

    /**
     * If this TriggerCommand is a redirect this will store
     * the target trigger string.
     *
     * @var string
     */
    public string $redirect = '';

    /**
     * @param string                                               $text
     * @param string                                               $topic
     * @param string                                               $type
     * @param \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue $queue
     */
    public function __construct(string $text, string $topic, string $type, ResponseQueue $queue)
    {
        $this->text = $text;
        $this->topic = $topic;
        $this->type = $type;
        $this->queue = $queue;
    }

    /**
     * Indicate if this trigger has responses
     * true or false.
     *
     * @return bool
     */
    public function hasResponses(): bool
    {
        return ($this->getQueue()->getResponses()->count() > 0);
    }

    /**
     * Return the response queue.
     *
     * @return \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue
     */
    public function getQueue(): ResponseQueue
    {
        return $this->queue;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function getRedirect(): string
    {
        return $this->redirect;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order = 0): void
    {
        $this->order = $order;
    }
}
