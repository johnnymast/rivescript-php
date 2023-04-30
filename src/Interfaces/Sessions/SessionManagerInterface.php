<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Interfaces\Sessions;

/**
 * SessionManagerInterface
 *
 * This interface will make sure all session managers
 * will implement the same functions.
 *
 * PHP version 8.1 and higher.
 *
 * @category Core
 * @package  Sessions
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface SessionManagerInterface
{
    /**
     * Make a snapshot of the user's variables.
     *
     * This should clone and store a snapshot of all stored variables for the
     * user, so that they can later be restored with ``thaw()``. This
     * implements the RiveScript freezeUservars() method.
     *
     * @param string $username The username to freeze variables for.
     *
     * @return void
     */
    public function freeze(string $username): void;

    /**
     * Retrieve a stored variable for a user.
     *
     * If the user doesn't exist, this should return NULL. If the user
     * does exist, but the key does not, this should return the
     * string value "undefined".
     *
     * @param string $username The username to retrieve variables for.
     * @param string $key      The specific variable name to retrieve.
     * @param string $default  The default value for key the key is not defined.
     *
     * @return mixed The value of the requested key, "undefined", or NULL.
     */
    public function get(string $username, string $key, string $default = "undefined"): mixed;

    /**
     * Retrieve all variables about all users.
     *
     * This should return a array of arrays, where the top level keys are the
     * usernames of every user your bot has data for, and the values are arrays
     * of key/value pairs of those users.
     *
     * For example:
     *
     *  {
     *   "user1": {
     *   "topic": "random",
     *   "name": "Alice",
     *   },
     *   "user2": {
     *   "topic": "random",
     *   "name": "Bob",
     *   },
     * }
     * @return mixed
     */
    public function getAll(): array;

    /**
     * Retrieve all stored variables for a user.
     *
     * If the user doesn't exist, this should return NULL.
     *
     * @param string $username The username to retrieve variables for.
     *
     * @return array|null Key/value pairs of all stored data for the user, or null.
     */
    public function getAny(string $username): array|null;

    /**
     * Reset all variables stored about a particular user.
     *
     * @param string $username The username to flush all data for.
     *
     * @return void
     */
    public function reset(string $username): void;

    /**
     * Reset all variables for all users.
     *
     * @return void
     */
    public function resetAll(): void;

    /**
     * Set variables for a user.
     *
     * @param string|null   $username  The username to set variables for.
     * @param array<string> $args      Associative array of key/value pairs variables to set for the user.
     *                                 The values are usually strings, but they can be other types
     *                                 as well (e.g. arrays or other objects) for some internal data
     *                                 structures such as input/reply history. A value of null
     *                                 should indicate that the key should be deleted from the session
     *                                 store.
     *
     * @return void
     */
    public function set(string $username = null, array $args = []): void;

    /**
     * Restore the frozen snapshot of variables for a user.
     *
     * This should replace all of a user's variables with the frozen copy
     * that was a snapshot created with the freeze() method. If there are no frozen
     * variables, this function should be a no-op (maybe issue a warning?)
     *
     * @param string $username The username to restore variables for.
     * @param string $action   An action to perform on the variables. Valid options are:
     *                         thaw: Restore the variables and delete the frozen copy (default).
     *                         discard: Don't restore the variables, just delete the frozen copy.
     *                         keep: Restore the variables and keep the copy still.
     *
     * @return void
     */
    public function thaw(string $username, string $action = "thaw"): void;
}