<?php

namespace Vulcan\Rivescript\Cortex\Triggers;

use Vulcan\Rivescript\Contracts\Trigger as TriggerContract;

abstract class Trigger implements TriggerContract
{
    public function triggerFound()
    {
        return true;
    }

    public function triggerNotFound()
    {
        return false;
    }
}
