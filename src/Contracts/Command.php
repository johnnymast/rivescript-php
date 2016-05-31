<?php

namespace Vulcan\Rivescript\Contracts;

interface Command
{
    public function parse($tree, $line, $command);
}
