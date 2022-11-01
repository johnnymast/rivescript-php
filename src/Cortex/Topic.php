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
use Axiom\Rivescript\Cortex\Commands\TriggerCommand;
use Axiom\Rivescript\Cortex\Tags\Tag;

/**
 * Topic class
 *
 * Stores information about a topic.
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
class Topic
{
    /**
     * The name of this Topic.
     *
     * @var string
     */
    protected string $name;

    /**
     * The triggers for this Topic.
     *
     * @var Collection<string, mixed>
     */
    public Collection $triggers;

    /**
     * The responses for this Topic.
     *
     * @var Collection<string, TriggerCommand>
     */
    public Collection $responses;

    /**
     * This indicates if the > begin topic
     * is ok to finish.
     *
     * @var bool
     */
    public bool $ok = false;

    /**
     * Create a new Topic instance.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->triggers = new Collection([]);
        $this->responses = new Collection([]);
    }

    /**
     * Addd a trigger to this topic.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return void
     */
    public function addTrigger(TriggerCommand $trigger): void
    {
        $this->triggers->push($trigger);
        $this->sortTriggers();
    }

    /**
     * Return triggers associated with this branch.
     *
     * @return Collection<string, mixed>
     */
    public function triggers(): Collection
    {
        return $this->triggers;
    }

    /**
     * Return the responses associated with this branch.
     *
     * @return Collection<string, mixed>
     */
    public function responses(): Collection
    {
        return $this->responses;
    }

    /**
     * Sorting +Triggers
     * Triggers should be sorted in a "most specific first" order. That is:
     *
     * 1. Atomic triggers first. Sort them so that the triggers with the most amount
     * of words are on top. For multiple triggers with the same amount of words,
     * sort them by length, and then alphabetically if there are still matches
     * in length.
     * 2. Sort triggers that contain optionals in their triggers next. Sort them in
     * the same manner as the atomic triggers.
     * 3. Sort triggers containing wildcards next. Sort them by the number of words
     * that aren't wildcards. The order of wildcard sorting should be as follows:
     *
     * A. Alphabetic wildcards (_)
     * B. Numeric wildcards (#)
     * C. Global wildcards (*)
     *
     * 4. The very bottom of the list will be a trigger that simply matches * by
     * itself, if it exists. If triggers of only _ or only # exist, sort them in
     * the same order as in step 3.
     *
     * Sorting %PreviousCommand
     * % PreviousCommand triggers should be sorted in the same manner as + Triggers, and associated with the reply
     * group that they belong to (creating pseudotopics for each % PreviousCommand is a good way to go).
     *
     * @return void
     */
    public function sortTriggers(): void
    {
        $triggers = $this->sortTriggersByType($this->triggers);
        // TODO: Order by word count
//         $triggers = $this->sortTriggersByWordCount($triggers);

        $this->triggers = $triggers->sort(function ($current, $previous) {
            return ($current->getOrder() < $previous->getOrder()) ? -1 : 1;
        })->reverse();


        $index = 1;
        $triggers->each(function ($trigger) use (&$index) {
            synapse()->rivescript->verbose(":index) :value", [
                'index' => $index,
                'value' => $trigger->node->getValue(),
            ]);

            $index++;
        });
    }

    /**
     * Determine the order in the triggers.
     *
     * @param Collection<array> $triggers A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function sortTriggersByType(Collection $triggers): Collection
    {
        return $triggers->each(function (TriggerCommand &$trigger) {

            $order = 4000000;

            if ($trigger->isFullyAtomic() === false) {

                if ($trigger->hasOptionals() === true) {
                    $order = 3000000;
                } else {
                    $order = 2000000;
                }
            }

            $trigger->setOrder($order);
        });
    }

    public function detectTrigger(): TriggerCommand|null
    {

        /** @var TriggerCommand $trigger */
        foreach ($this->triggers as $trigger) {
            TagRunner::run(Tag::TRIGGER, $trigger);
            $value = $trigger->parse();

            if ($value) {
                return $trigger;
            }
        }

        return null;
    }

    /**
     * Sort triggers based on word count from
     * largest to smallest.
     *
     * @param Collection<array> $triggers A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function sortTriggersByWordCount(Collection $triggers): Collection
    {

        foreach ($triggers as &$trigger) {
            if ($trigger->getType() === 'atomic' || $trigger->hasOptionals() === true) {
                $trigger->setOrder(count(explode(' ', $trigger->getNode()->getValue())));
            }
        }

        return $triggers;
    }

    /**
     * Return the name of the topic.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function isBegin(): bool
    {
        return ($this->name === "__begin__");
    }

    /**
     * Set the ok value.
     *
     * @param bool $value Ok value true or false.
     *
     * @return void
     */
    public function setOk(bool $value = false): void
    {
        $this->ok = $value;
    }

    /**
     * Indicate if this topic is ok to end
     * or not.
     *
     * Note: This is only used for the __begin__
     * topic.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->ok;
    }
}
