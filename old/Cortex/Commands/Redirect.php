<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commandss;

use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;

/**
 * RedirectCommand class
 *
 * This class handles the RedirectCommand commands (@ ...)
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
class Redirect implements Command
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
        if ($node->command() === '@') {
            $topic = synapse()->memory->shortTerm()->get('topic') ?: 'random';
            $trigger = null;
            $key = -1;

            // FIXME ugly code award make this global.
            foreach (synapse()->brain->topic($topic)->triggers() as $index => $info) {
                if ($info->getText() === $key) {
                    $trigger = $info;
                    $key = $index;
                    break;
                }
            }

            if ($key > -1) {
                $trigger['redirect'] = $node->value();
                $trigger['value'] = $node->value();


                synapse()->brain->topic($topic)->triggers()->put($key, $trigger);
            }
        }
    }
}
