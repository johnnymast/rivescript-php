<?php

/**
 * Definition of the Command contract.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Contracts
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Contracts;

use Axiom\Rivescript\Cortex\Node;

interface Command
{
    /**
     * Parse the command.
     *
     * @param  Node  $node  The node is a line from the Rivescript file.
     *
     * @return void
     */
    public function parse(Node $node);
}
