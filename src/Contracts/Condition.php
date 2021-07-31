<?php


namespace Axiom\Rivescript\Contracts;

use Axiom\Rivescript\Cortex\Node;

interface Condition
{
    public function parse(string $source);
}