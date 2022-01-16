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

use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;

/**
 * Response class
 *
 * This class handles the Response commands (> ..., * ..., ^ ...)
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
class Response implements Command
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
        if ($node->command() === '-' || $node->command() === '*' || $node->command() === '^') {
            $topic = synapse()->memory->shortTerm()->get('topic') ?: 'random';
            $key = synapse()->memory->shortTerm()->get('trigger');
            $trigger = synapse()->brain->topic($topic)->triggers()->get($key);

            echo "Attach {$node->value()} to {$key} for topic {$topic}\n";
            $trigger['responses']->attach($node);

            synapse()->brain->topic($topic)->triggers()->put($key, $trigger);
        }
    }
}
