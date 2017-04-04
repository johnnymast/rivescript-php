<?php

namespace Vulcan\Rivescript\Cortex\Commands;

use Vulcan\Rivescript\Contracts\Command;

class Trigger implements Command
{
    /**
     * Parse the command.
     *
     * @param  Node  $node
     * @param  String  $command
     * @return array
     */
    public function parse($node, $command)
    {
        if ($node->command() === '+') {
            $topic = synapse()->memory->shortTerm()->get('topic') ?: 'random';
            $topic = synapse()->brain->topic($topic);

            $tag = $this->tagTrigger($node->value());

            $topic->triggers->put($node->value(), []);

            $topic->triggers = $this->sortTriggers($topic->triggers);
            
            synapse()->memory->shortTerm()->put('trigger', $node->value());
        }
    }

    protected function tagTrigger($trigger)
    {
        return '';
    }

    protected function sortTriggers($triggers)
    {
        $sortedTriggers = $triggers->sortByKey(function($current, $previous) {
            if ($current === $previous) {
                return 0;
            }

            $currentWordCount  = count(explode(' ', $current));
            $previousWordCount = count(explode(' ', $previous));

            return ($currentWordCount < $previousWordCount) ? -1 : 1;
        })->reverse();

        return $sortedTriggers;
    }
}
