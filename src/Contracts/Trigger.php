<?php

namespace Vulcan\Rivescript\Contracts;

interface Trigger
{
    /**
     * Parse the trigger.
     *
     * @param  integer  $key
     * @param  string  $trigger
     * @param  string  $message
     * @return array
     */
    public function parse($key, $trigger, $message);
}
