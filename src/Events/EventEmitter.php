<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Events;

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
     * @param string          $event   The event string.
     * @param string|callable $handler The event handler.
     *
     * @return self
     */
    public function on(string $event, string|callable $handler): self
    {
        if (!isset($this->registered[$event])) {
            $this->registered[$event] = [];
        }

        $this->registered[$event][] = $handler;

        return $this;
    }

    /**
     * Send out a message.
     *
     * @param string $event       The event string.
     * @param mixed  ...$userdata Arguments for the callback.
     *
     * @return self
     */
    public function emit(string $event, ...$userdata): self
    {
        if (isset($this->registered[$event]) === true) {
            $set = $this->registered[$event];

            foreach ($set as $handler) {
                call_user_func_array($handler, $userdata);
            }
        }

        return $this;
    }
}
