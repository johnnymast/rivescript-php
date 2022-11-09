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

use Axiom\Rivescript\Exceptions\MemorySessionException;

/**
 * MemorySessionStorage class
 *
 * This is the default in-memory session store for RiveScript.
 *
 * It keeps all user variables in an object in memory and does not persist them
 * to disk. This means it won't remember user variables between reboots of your
 * bot's program, but it remembers just fine during its lifetime.
 *
 * The RiveScript methods `getUservars()` and `setUservars()` are available to
 * export and import user variables as JSON-serializable objects so that your
 * program could save them to disk on its own.
 *
 * See the documentation for `SessionManager` for information on extending
 * RiveScript with an alternative session store.
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
class MemorySessionStorage extends Session
{
    /**
     * @param array<string, mixed> $users  A list settings for the users.
     * @param array<string, mixed> $frozen A list of frozen settings.
     */
    public function __construct(
        protected array $users = [],
        protected array $frozen = [],
    )
    {
        // Yankee doodle
    }

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
    public function set(string $username, array $args): void
    {
        if (!isset($this->users[$username])) {
            $this->users[$username] = $this->defaultSession();
        }
        foreach ($args as $key => $value) {
            $this->users[$username][$key] = $value;
        }
    }

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
    public function get(string $username, string $key, string $default = "undefined"): mixed
    {
        if (!isset($this->users[$username])) {
            return null;
        }
        if (!isset($this->users[$username][$key])) {
            return "undefined";
        }
        return $this->users[$username][$key];
    }

    /**
     * Retrieve all stored variables for a user.
     *
     * If the user doesn't exist, this should return null.
     *
     * @param string $username The username to retrieve variables for.
     *
     * @return array<string, mixed>|null Key/value pairs of all stored data for the user, or null.
     */
    public function getAny(string $username): array|null
    {
        return $this->users[$username];
    }

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
    public function getAll(): array
    {
        return $this->users;
    }

    /**
     * Reset all variables stored about a particular user.
     *
     * @param string $username The username to flush all data for.
     *
     * @return void
     */
    public function reset(string $username): void
    {
        if (isset($this->users[$username])) {
            unset($this->users[$username]);
        }
    }

    /**
     * Reset all variables for all users.
     *
     * @return void
     */
    public function resetAll(): void
    {
        $this->users = [];
        $this->frozen = [];
    }

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
    public function freeze(string $username): void
    {
        if (!isset($this->users[$username])) {
            throw new MemorySessionException("thaw({$username}): no frozen variables found");
        }

        $this->frozen[$username] = $this->users[$username];
    }

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
    public function thaw(string $username, string $action = "thaw"): void
    {
        if (isset($this->frozen[$username])) {
            switch ($action) {
                case "thaw":
                    $this->users[$username] = $this->frozen[$username];
                    unset($this->frozen[$username]);
                    break;
                case "discard":
                    unset($this->frozen[$username]);
                    break;
                case "keep":
                    $this->users[$username] = $this->frozen[$username];
                    break;
                default:
                    throw new MemorySessionException("bad thaw action");
            }
        } else {
            throw new MemorySessionException("thaw({$username}): no frozen variables found");
        }
    }

    /**
     * /**
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
    public function defaultSession(): mixed
    {
        return [
            "topic" => "random"
        ];
    }
}