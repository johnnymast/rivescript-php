<?php

/**
 * This class parses the Topic command type.
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
 * Class Topic
 */
class Topic implements Command
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
        if ($node->command() === '>') {
            list($type, $topic) = explode(' ', $node->value());

            if ($type === 'topic') {
                if (!synapse()->brain->topic($topic)) {
                    synapse()->brain->createTopic($topic);
                }

                synapse()->memory->shortTerm()->put('topic', $topic);
            }
        }

        if ($node->command() === '<' and $node->value() === 'topic') {
            synapse()->memory->shortTerm()->remove('topic');
        }
    }
}
