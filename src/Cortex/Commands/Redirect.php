<?php

/**
 * This class parses the Redirect command type.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Commands
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Contracts\Command;
use Axiom\Rivescript\Cortex\Node;

/**
 * Class Redirect
 */
class Redirect implements Command
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
        if ($node->command() === '@') {
            $topic   = synapse()->memory->shortTerm()->get('topic') ?: 'random';
            $key     = synapse()->memory->shortTerm()->get('trigger');
            $trigger = synapse()->brain->topic($topic)->triggers()->get($key);

            $trigger['redirect'] = $node->value();

            synapse()->brain->topic($topic)->triggers()->put($key, $trigger);
        }
    }
}
