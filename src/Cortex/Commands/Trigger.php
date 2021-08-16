<?php

/**
 * This class parses the Trigger command type.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Commands
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;

/**
 * Class Trigger
 */
class Trigger implements Command
{
    /**
     * Parse the command.
     *
     * @param  Node  $node  The node is a line from the Rivescript file.
     *
     * @return void
     */
    public function parse(Node $node)
    {
        if ($node->command() === '+') {
            $topic = synapse()->memory->shortTerm()->get('topic') ?: 'random';
            $topic = synapse()->brain->topic($topic);
            $type = $this->determineTriggerType($node->value());

            $data = [
                'type' => $type,
                'responses' => new ResponseQueue(),
            ];

            $topic->triggers->put($node->value(), $data);

            $topic->triggers = $this->sortTriggers($topic->triggers);

            synapse()->memory->shortTerm()->put('trigger', $node->value());
        }
    }

    /**
     * Determine the type of trigger to aid in sorting.
     *
     * @param  string  $trigger  The trigger to parse.
     *
     * @return string
     */
    protected function determineTriggerType(string $trigger): string
    {
        $wildcards = [
            'alphabetic' => '/_/',
            'numeric' => '/#/',
            'global' => '/\*/',
        ];

        foreach ($wildcards as $type => $pattern) {
            if (@preg_match_all($pattern, $trigger, $stars)) {
                return $type;
            }
        }

        return 'atomic';
    }

    /**
     * Sort triggers based on type and word count from
     * largest to smallest.
     *
     * @param  Collection<array>  $triggers  A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function sortTriggers(Collection $triggers): Collection
    {
        $triggers = $this->determineWordCount($triggers);
        $triggers = $this->determineTypeCount($triggers);
        return $triggers->sort(function ($current, $previous) {
            return ($current['order'] < $previous['order']) ? -1 : 1;
        })->reverse();
    }

    /**
     * Determine the order in the triggers.
     *
     * @param  Collection<array>  $triggers  A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function determineTypeCount(Collection $triggers): Collection
    {
        return $triggers->each(function ($data, $trigger) use ($triggers) {
            if (isset($data['type'])) {
                switch ($data['type']) {
                    case 'atomic':
                        $data['order'] += 4000000;
                        break;
                    case 'alphabetic':
                        $data['order'] += 3000000;
                        break;
                    case 'numeric':
                        $data['order'] += 2000000;
                        break;
                    case 'global':
                        $data['order'] += 1000000;
                        break;
                }

                $triggers->put($trigger, $data);
            }
        });
    }

    /**
     * Sort triggers based on word count from
     * largest to smallest.
     *
     * @param  Collection<array>  $triggers  A collection of triggers.
     *
     * @return Collection<array>
     */
    protected function determineWordCount(Collection $triggers): Collection
    {
        return $triggers->each(function ($data, $trigger) use ($triggers) {
            $data['order'] = count(explode(' ', $trigger));

            $triggers->put($trigger, $data);
        });
    }
}
