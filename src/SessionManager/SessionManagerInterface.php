<?php

namespace Axiom\Rivescript\SessionManager;

interface SessionManagerInterface
{
    /**
     * @param string $key   Save a value using this key.
     * @param mixed  $value The value to set.
     *
     * @return bool
     */
    public function set(string $key, mixed $value): bool;

    /**
     * Return the value of a key.
     *
     * @param string $key Return the value from this key.
     *
     * @return string
     */
    public function get(string $key): string;

    /**
     * Remove a value from the session.
     *
     * @param string $key Data with this key will be removed.
     *
     * @return bool
     */
    public function unset(string $key): bool;

    /**
     * Check if a key exists.
     *
     * @param string $key The key to check for.
     *
     * @return bool
     */
    public function has(string $key): bool;
}