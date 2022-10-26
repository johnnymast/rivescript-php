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
use Axiom\Rivescript\Cortex\Commands\ResponseCommand;
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
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseCommand $response The response to add.
     * @param string                                            $type     The type of response to archive it as.
     *
     * @return void
     */
    public function attach(ResponseCommand $response, string $type): void
    {
        if ($this->responses->has($type) === false) {
            $this->responses->put($type, Collection::make([]));
        }
        $this->responses->get($type)->push($response);
    }

    /**
     * Return the responses.
     *
     * @return \Axiom\Collections\Collection<ResponseCommand>
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
     * Process the response queue.
     *
     * @return \Axiom\Rivescript\Cortex\Commands\ResponseCommand|null
     */
    public function process(): ?ResponseCommand
    {
        $response = null;

        // order same as trigger.
        // parse tags
        //  TagRunner::run();

        if ($this->responses->has('atomic')) {
            foreach ($this->responses->get('atomic') as $type => $command) {
                TagRunner::run(Tag::RESPONSE, $command);
                return $command;
            }
        }

        return $response;
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
