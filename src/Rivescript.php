<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript;

use Axiom\Rivescript\ContentLoader\ContentLoader;
use Axiom\Rivescript\Exceptions\Sessions\MemorySessionException;
use Axiom\Rivescript\Interfaces\Sessions\SessionManagerInterface;
use Axiom\Rivescript\Sessions\MemorySessionManager;

/**
 * Rivescript class
 *
 * @method void load(string|array $path)
 * @method void loadDirectory(string $path)
 * @method void loadFile(string $filename)
 * @method mixed getStream()
 *
 *
 * The entry point for using the interpreter.
 *
 * PHP version 8.1 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Rivescript extends ContentLoader
{

    /**
     * A RiveScript interpreter for PHP.
     *
     * @param bool $debug  Set to true to enable verbose logging to standard out.
     * @param bool $strict Enable strict mode. Strict mode causes RiveScript syntax
     *                     errors to raise an exception at parse time. Strict mode is on
     *                     true by default.
     * @param int  $depth  Enable strict mode. Strict mode causes RiveScript syntax
     *                     errors to raise an exception at parse time. Strict mode is on
     *                     true by default.
     * @param bool $utf8   Enable UTF-8 mode. When this mode is enabled, triggers in
     *                     RiveScript code are permitted to contain foreign and special
     *                     symbols. Additionally, user messages are allowed to contain most
     *                     symbols instead of having all symbols stripped away. This is
     *                     considered an experimental feature because all the edge cases of
     *                     supporting Unicode haven't been fully tested. This option
     *                     is false by default.
     */
    public function __construct(
        protected bool $debug = false,
        protected bool $strict = true,
        protected int $depth = 50,
        protected bool $utf8 = false,
        protected SessionManagerInterface $sessionManager = new MemorySessionManager(),
    ) {
    }

    /**
     * Set a variable for a user.
     * This is like the ``<set>`` tag in RiveScript code.
     *
     * @param string $user  The user ID to set a variable for.
     * @param string $name  The name of the variable to set.
     * @param string $value he value to set there.
     *
     * @return void
     */
    public function setUserVar(string $user, string $name, string $value): void
    {
        if ($name == "topic" && $this->forceCase) {
            $value = strtolower($value);
        }

        $fields = [$name => $value];
        $this->sessionManager->set($user, $fields);
    }

    /**
     * Get all variables about a user (or all users).
     *
     * @param string|null $user The user ID to retrieve all variables for.
     *                          If not passed, this function will return all data for all users.
     *
     * @return array|null An array of key/value pairs, or null if the user doesn't exist.
     */
    public function getUserVars(string|null $user): array|null
    {
        if (!$user) {
            return $this->sessionManager->getAll();
        }

        return $this->sessionManager->getAny($user);
    }

    /**
     * Delete all variables about a user (or all users).
     *
     * @param string $user The user ID to clear variables for, or else clear all
     *                     variables for all users if not provided.
     *
     * @return void
     */
    public function clearUserVars(string $user): void
    {
        $this->sessionManager->reset($user);
    }

    /**
     * Freeze the variable state for a user.
     * This will clone and preserve a user's entire variable state, so that it
     * can be restored later with ``thaw_uservars()``.
     *
     *
     * @param string $user The user ID to freeze variables for.
     *
     * @return void
     */
    public function freezeUserVars(string $user): void
    {
        $this->sessionManager->freeze($user);
    }

    /**
     * Thaw a user's frozen variables.
     *
     * @param string $user   The user to perform the action for.
     * @param string $action the action to perform when thawing the variables:
     *                       ``discard``: Don't restore the user's variables, just delete the frozen copy.
     *                       ``keep``: Keep the frozen copy after restoring the variables.
     *                       ``thaw``: Restore the variables, then delete the frozen copy (this is the default).
     *
     * @throws \Axiom\Rivescript\Exceptions\Sessions\MemorySessionException
     *
     * @return void
     */
    public function thawUserVars(string $user, string $action = "thaw"): void
    {
        $this->sessionManager->thaw($user, $action);
    }
}
