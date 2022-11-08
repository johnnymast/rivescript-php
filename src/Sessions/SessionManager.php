<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\SessionManager;

/**
 * SessionManager class
 *
 * This is the Base class for session management for RiveScript. The session manager
 * keeps track of getting and setting user variables,for example when the <set> or <get> tags
 * are used in RiveScript or when the API functions like ``setUserVar()`` are called.
 *
 * By default RiveScript stores user sessions in memory and provides methods
 * to export and import them (e.g. to persist them when the bot shuts down
 * so they can be reloaded). If you'd prefer a more 'active' session storage,
 * for example one that puts user variables into a database or cache, you can
 * create your own session manager that extends this class and implements its
 * functions.
 *
 * See the ``eg/sessions`` example from the source of rivescript-python at
 * https://github.com/aichaos/rivescript-python for an example.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Sessions
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
abstract class SessionManager
{
    /**
     * Set variables for a user.
     *
     * @param string               $username The username to set variables for.
     * @param array<string, mixed> $args     array with Key/value pairs of variables to set for the user.
     *                                       The values are usually strings, but they can be other types
     *                                       as well (e.g. arrays or other dicts) for some internal data
     *                                       structures such as input/reply history. A value of ``null``
     *                                       should indicate that the key should be deleted from the session
     *                                       store.
     *
     * @return void
     */
    abstract public function set(string $username, array $args): void;

    /**
     * Retrieve a stored variable for a user.
     *
     * If the user doesn't exist, this should return ``null``. If the user
     * does* exist, but the key does not, this should return the
     * string value ``"undefined"``.
     *
     * @param string $username The username to retrieve variables for.
     * @param string $key      The specific variable name to retrieve.
     * @param string $default  The default value for key the key is not defined.
     *
     * @return mixed The value of the requested key, "undefined", or null.
     */
    abstract public function get(string $username, string $key, string $default = "undefined"): mixed;

    /**
     * Retrieve all stored variables for a user.
     *
     * If the user doesn't exist, this should return ``null``.
     *
     * @param string $username The username to retrieve variables for.
     *
     * @return array<string, mixed>|null Key/value pairs of all stored data for the user, or null.
     */
    abstract public function getAny(string $username): array|null;

    /**
     * Retrieve all variables about all users.
     *
     * This should return an object that maps usernames to an object of their
     * variables. For example:
     *
     *  { "user1": {
     *      "topic": "random",
     *      "name": "Alice",
     *  },
     *  "user2": {
     *      "topic": "random",
     *      "name": "Bob",
     *  },
     *  }
     * @return array <string, mixed>
     */
    abstract public function getAll(): array;

    /**
     * Reset all variables stored about a particular user.
     *
     * @param string $username The username to flush all data for.
     *
     * @return void
     */
    abstract public function reset(string $username): void;

    /**
     * Reset all variables for all users.
     *
     * @return void
     */
    abstract public function resetAll(): void;

    /**
     * Make a snapshot of the user's variables.
     *
     * This should clone and store a snapshot of all stored variables for the
     * user, so that they can later be restored with ``thaw()``. This
     * implements the RiveScript ``freezeUservars()`` method.
     *
     * @param string $username The username to freeze variables for.
     *
     * @throws \Axiom\Rivescript\Exceptions\MemorySessionException
     *
     * @return void
     */
    abstract public function freeze(string $username): void;

    /**
     * Restore the frozen snapshot of variables for a user.
     *
     * This should replace *all* of a user's variables with the frozen copy
     * that was snapshotted with ``freeze()``. If there are no frozen
     * variables, this function should be a no-op (maybe issue a warning?)
     *
     * @param string $username The username to restore variables for.
     * @param string $action   An action to perform on the variables. Valid options are:
     *                         ``thaw``: Restore the variables and delete the frozen copy (default).
     *                         ``discard``: Don't restore the variables, just delete the frozen copy.
     *                         ``keep``: Restore the variables and keep the copy still.
     *
     * @throws \Axiom\Rivescript\Exceptions\MemorySessionException
     *
     * @return void
     */
    abstract public function thaw(string $username, string $action = "thaw"): void;

    /**
     * The default session data for a new user.
     *
     * You do not need to override this function. This returns a ``dict`` with
     * the default key/value pairs for new sessions. By default, the
     * session variables are as follows::
     *
     * {
     * "topic": "random"
     * }
     * @return mixed  dict: A dict of default key/value pairs for new user sessions.
     */
    abstract public function defaultSession(): mixed;
}
