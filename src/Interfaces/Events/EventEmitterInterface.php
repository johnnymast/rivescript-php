<?php

namespace Axiom\Rivescript\Interfaces\Events;

use Axiom\Rivescript\RivescriptEvent;

interface EventEmitterInterface
{
    /**
     * Receive a message.
     *
     * @param RivescriptEvent $event   The event string.
     * @param string|callable $handler The event handler.
     *
     * @return self
     */
    public function on(RivescriptEvent $event, string|callable $handler): self;

    /**
     * Send out a message.
     *
     * @param RivescriptEvent $event       The event string.
     * @param mixed  ...$userdata Arguments for the callback.
     *
     * @return self
     */
    public function emit(RivescriptEvent $event, mixed ...$userdata): self;
}