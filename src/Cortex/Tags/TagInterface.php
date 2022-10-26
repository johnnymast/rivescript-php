<?php

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Commands\Command;

interface TagInterface
{
    public function parse(Command $command): void;
}