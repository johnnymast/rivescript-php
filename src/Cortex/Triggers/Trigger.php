<?php

namespace Vulcan\Rivescript\Cortex\Triggers;

use Vulcan\Rivescript\Contracts\Trigger as TriggerContract;

abstract class Trigger implements TriggerContract
{
    public function triggerFound($data = [])
    {
        return [
            'match' => true,
            'data'  => $data
        ];
    }

    public function triggerNotFound()
    {
        return [
            'match' => false
        ];
    }
}
