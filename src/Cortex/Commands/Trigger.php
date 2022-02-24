<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;
use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;

/**
 * Trigger class
 *
 * This class handles the Trigger command (+ ...)
 * and stores the definition in memory.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Trigger implements Command
{
    /**
     * Parse the command.
     *
     * @param Node $node The node is a line from the Rivescript file.
     *
     * @return void
     */
    public function parse(Node $node): void
    {
        if ($node->command() === '+') {
            $topic = synapse()->memory->shortTerm()->get('topic') ?: 'random';

            $type = $this->determineTriggerType($node->value());

            $data = [
                'topic' => $topic,
                'type' => $type,
                'responses' => new ResponseQueue($node->value()),
                'value' => $node->value()
            ];

            $topic = synapse()->brain->topic($topic);
            $topic->triggers->put($node->value(), $data);
            $topic->triggers = $topic->sortTriggers($topic->triggers());

            synapse()->memory->shortTerm()->put('trigger', $node->value());
        }
    }

    /**
     * Determine the type of trigger to aid in sorting.
     *
     * @param string $trigger The trigger to parse.
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
}
