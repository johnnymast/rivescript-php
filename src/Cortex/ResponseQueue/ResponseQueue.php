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

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\Commands\ResponseAbstract;
use Axiom\Rivescript\Cortex\Commands\ResponseInterface;
use Axiom\Rivescript\Cortex\Commands\TriggerCommand;
use Axiom\Rivescript\Cortex\TagRunner;
use Axiom\Rivescript\Cortex\Tags\Tag;

/**
 * ResponseQueue class
 *
 * The ResponseQueue is responsible for storing responses
 * for triggers.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\ResponseQueue
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ResponseQueue
{

    /**
     * A container with responses.
     *
     * @var Collection<ResponseQueueItem>
     */
    protected Collection $responses;

    /**
     * Reference to the parent TriggerCommand.
     *
     * @var \Axiom\Rivescript\Cortex\Commands\TriggerCommand
     */
    protected TriggerCommand $trigger;

    /**
     * ResponseQueue constructor.
     */
    public function __construct(TriggerCommand $trigger)
    {
        $this->responses = new Collection([]);
        $this->trigger = $trigger;
    }

    /**
     * Attach a new response.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseInterface $response The response to add.
     *
     * @return void
     */
    public function attach(ResponseInterface $response): void
    {
        $queueItem = new ResponseQueueItem($response, $this->responses->count());

        /**
         * @var \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem|bool $last ;
         */
        $last = $this->responses->last();

        if (!$last || $response->getType() != 'continue') {
            $this->responses->push($queueItem);
        } else if ($response->getType() === 'continue') {
            $last->addContinue($response);
        }
    }

    /**
     * Return the responses.
     *
     * @return \Axiom\Collections\Collection<\Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    /**
     * Return the trigger.
     *
     * @return \Axiom\Rivescript\Cortex\Commands\TriggerCommand
     */
    public function getTrigger(): TriggerCommand
    {
        return $this->trigger;
    }

    /**
     * Validate the Response queue item. This could be a condition
     * or a check to see if all information is present. If the response
     * gets validated it continues on in the queue.
     *
     * @param \Axiom\Collections\Collection<\Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem> $responses
     *
     * @return \Axiom\Collections\Collection<\Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem>
     */
    private function validateResponses(Collection $responses): Collection
    {
        $validResponses = Collection::make([]);

        foreach ($responses as $response) {
            if ($response->validate()) {
                $validResponses->push($response);
            }
        }

        return $validResponses;
    }

    /**
     * Process the response queue.
     *
     * @return \Axiom\Rivescript\Cortex\Commands\ResponseAbstract|null
     */
    public function process(): string|bool
    {
        $response = null;

        // order same as trigger.
        // parse tags
        //  TagRunner::run();

        $responses = $this->getResponses();
        $responses = $this->validateResponses($responses);

        if ($responses->count() > 0) {
            return $responses->first()->render();

        }

        return false;
//        //       if ($this->responses->has('atomic')) {
//        foreach ($responses->get('atomic') as $response) {
//            //    $command->reset();
//            $response->invokeStars();
//            TagRunner::run(Tag::RESPONSE, $response);
//            return $response;
//        }
//
//
//        return $response;
    }

    /**
     * Check if there are responses in the queue.
     *
     * @return bool
     */
    public function hasResponses(): bool
    {
        return (count($this->responses) > 0);
    }
}
