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
 * Trigger class
 *
 * This class contains information about
 * a Trigger itself.
 *
 * PHP version 7.4 and higher.
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

    public string $topic = '';

    public string $type = '';

    public int $order = 0;

    public ResponseQueue $queue;

    public string $redirect = '';


    /**
     * @param string                                               $text
     * @param string                                               $topic
     * @param string                                               $type
     * @param \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue $responses
     */
    public function __construct(string $text, string $topic, string $type, ResponseQueue $queue)
    {
        $this->text = $text;
        $this->topic = $topic;
        $this->type = $type;
        $this->queue = $queue;
    }

    public function hasResponses(): bool
    {
        return ($this->getQueue()->getAttachedResponses()->count() > 0);
    }

    public function getQueue(): Collection
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

    public function setRedirect(string $redirect)
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
