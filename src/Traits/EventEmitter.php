<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Traits;

use Axiom\Rivescript\RivescriptEvent;

/**
 * EventEmitter trait
 *
 * This trait allows the Rivescript-php core
 * to communicate with the developer using the
 * interpreter.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Events
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
trait EventEmitter
{
    protected array $registered = [];

    /**
     * Receive a message.
     *
     * @param RivescriptEvent $event   The event string.
     * @param string|callable $handler The event handler.
     *
     * @return self
     */
    public function on(RivescriptEvent $event, string|callable $handler): self
    {
        if (!isset($this->registered[$event->value])) {
            $this->registered[$event->value] = [];
        }

        $this->registered[$event->value][] = $handler;

        return $this;
    }

    /**
     * Send out a message.
     *
     * @param RivescriptEvent $event       The event string.
     * @param mixed           ...$userdata Arguments for the callback.
     *
     * @return self
     */
    public function emit(RivescriptEvent $event, mixed ...$userdata): self
    {
        if (isset($this->registered[$event->value]) === true) {
            $set = $this->registered[$event->value];

            foreach ($set as $handler) {
                call_user_func_array($handler, $userdata);
            }
        }

        return $this;
    }
}