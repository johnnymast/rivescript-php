<?php

namespace Vulcan\Rivescript\Contracts;

interface Trigger
{
    public function parse($key, $trigger, $message);
}
