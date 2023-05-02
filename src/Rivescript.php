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
use Axiom\Rivescript\Interfaces\Events\EventEmitterInterface;
use Axiom\Rivescript\Interfaces\Sessions\SessionManagerInterface;
use Axiom\Rivescript\Messages\MessageType;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\Parser\Parser;
use Axiom\Rivescript\Sessions\MemorySessionManager;
use Axiom\Rivescript\Traits\EventEmitter;
use Axiom\Rivescript\Utils\Misc;
use Traits\EventEmitte;

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
    use EventEmitter;

    protected ?Brain $brain = null;
    protected ?Parser $parser = null;

    /**
     * The default concatenation mode.
     *
     * @var string
     */
    public string $concat = "none";

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
        protected ?SessionManagerInterface $session = new MemorySessionManager(),
    ) {

        $this->parser = new Parser(
            master: $this,
            strict: $this->strict,
            utf8: $this->utf8
        );

        $this->tabObjects(
            [
                $this->session,
                $this->parser
            ]
        );
    }

    private function tabObjects(array $objects): void
    {
        foreach ($objects as $object) {
            if ($object instanceof EventEmitterInterface) {
                $object->on(RivescriptEvent::OUTPUT, function (RivescriptMessage $event) {
                    switch ($event->type) {
                        case MessageType::DEBUG:
                            $this->debug($event->message, $event->args);
                            break;
                        case MessageType::WARNING:
                            $this->warn($event->message, $event->args);
                            break;
                        case MessageType::ERROR:
                            $this->error($event->message, $event->args);
                            break;
                        case MessageType::SAY:
                            $this->say($event->message, $event->args);
                            break;
                    }
                });
            }
        }
    }

    /**
     * Set a variable for a user.
     * This is like the ``<set>`` tag in RiveScript code.
     *
     * @param string $user  The user ID to set a variable for.
     * @param string $name  The name of the variable to set.
     * @param mixed  $value The value to set.
     *
     * @return void
     */
    public function setUserVar(string $user, string $name, mixed $value): void
    {
        $fields = [$name => $value];
        $this->session->set($user, $fields);
    }

    /**
     * Set many variables for a user, or set many variables for many users.
     *
     * This function can be called in two ways:
     *
     * User variables for a single user:
     * $rivescript->setUserVars("username", ['name' => 'bob']);
     *
     * User variables for many users:
     * $rivescript->setUserVars(NULL, ['username1' => ['name' => 'bob'], 'username2' => ['name' => 'alice']]);
     * or
     * $rivescript->setUserVars(data: ['username1' => ['name' => 'bob'], 'username2' => ['name' => 'alice']]);
     *
     * This way you can export all user variables via getUserVars()
     * and then re-import them all at once, instead of setting them once per
     * user.
     *
     * @param string|null $user The user ID to set many variables for.
     *                          Skip this parameter to set many variables for many users instead.
     * @param array       $data An array of key/value pairs for user variables,
     *                          or else an array of arrays mapping usernames to key/value pairs.
     *
     * @return void
     */
    public function setUserVars(string|null $user = null, array $data = []): void
    {
        $this->session->set($user, $data);
    }

    /**
     * Get a variable about a user.
     *
     * @param string $user The user ID to look up a variable for.
     * @param string $name The name of the variable to get.
     *
     * @return mixed The value of the requested key, "undefined", or NULL.
     */
    public function getUserVar(string $user, string $name): mixed
    {
        return $this->session->get($user, $name);
    }

    /**
     * Get all variables about a user (or all users).
     *
     * @param string|null $user The user ID to retrieve all variables for.
     *                          If not passed, this function will return all data for all users.
     *
     * @return array|null An array of key/value pairs, or null if the user doesn't exist.
     */
    public function getUserVars(string|null $user = null): array|null
    {
        if (!$user) {
            return $this->session->getAll();
        }

        return $this->session->getAny($user);
    }

    /**
     * Delete all variables about a user (or all users).
     *
     * @param string|null $user The user ID to clear variables for, pass null
     *                          to remove all user variables.
     *
     * @return void
     */
    public function clearUserVars(string|null $user = null): void
    {
        if (is_null($user)) {
            $this->session->resetAll();
        } else {
            $this->session->reset($user);
        }
    }

    /**
     * Freeze the variable state for a user.
     * This will clone and preserve a user's entire variable state, so that it
     * can be restored later with thawUserVars().
     *
     * @param string $user The user ID to freeze variables for.
     *
     * @return void
     */
    public function freezeUserVars(string $user): void
    {
        $this->session->freeze($user);
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
        $this->session->thaw($user, $action);
    }

    /**
     * Write a warning.
     *
     * @param string        $message The message to print out.
     * @param array<string> $args    (format) extra parameters.
     *
     * @return void
     */
    public function debug(string $message, array $args = []): void
    {
        $message = "[DEBUG] " . Misc::formatString($message, $args);

        $this->emit(RivescriptEvent::DEBUG, $message);
    }

    /**
     * Write a verbose debug message.
     *
     * @param string        $message The message to print out.
     * @param array<string> $args    (format) extra parameters.
     *
     * @return void
     */
    public function verbose(string $message, array $args = []): void
    {
        $message = "[VERBOSE] " . Misc::formatString($message, $args);

        $this->emit(RivescriptEvent::VERBOSE, $message);
    }

    /**
     * Write a debug message.
     *
     * @param string        $message The message to print out.
     * @param array<string> $args    (format) arguments for the message.
     *
     * @return void
     */
    public function warn(string $message, array $args = []): void
    {
        $message = "[WARNING] " . Misc::formatString($message, $args);

        $this->emit(RivescriptEvent::WARNING, $message);
    }

    /**
     * Write a error message.
     *
     * @param string        $message The message to print out.
     * @param array<string> $args    (format) extra parameters.
     *
     * @return void
     */
    public function error(string $message, array $args = []): void
    {
        $message = "[ERROR] " . Misc::formatString($message, $args);

        $this->emit(RivescriptEvent::ERROR, $message);
    }

    /**
     * Log a message to say.
     *
     * @param string        $message The message to print out.
     * @param array<string> $args    (format) arguments for the message.
     *
     * @return void
     */
    public function say(string $message, array $args = []): void
    {
        $message = Misc::formatString($message, $args);

        $this->emit(RivescriptEvent::SAY, $message);
    }

    /**
     * Sort the loaded triggers in memory.
     *
     * After you have finished loading your RiveScript code, call this method
     * to populate the various internal sort buffers. This is absolutely
     * necessary for reply matching to work efficiently!
     *
     * @return void
     */
    public function sortReplies(): void
    {
        $this->say("Sorting triggers...");
    }

    /**
     * Define a custom language handler for RiveScript objects.
     *
     * Pass in a NULL value for the object to delete an existing handler (for
     * example, to prevent Python code from being able to be run by default).
     *
     * @param string $language The lowercase name of the programming language.
     *                         Examples: python, javascript, perl
     * @param mixed  $object   An instance of an implementation class object.
     *
     * @return void
     */
    public function setHandler(string $language, mixed $object): void
    {
        if (!$object) {
            // delete the handler
        }
    }


    /**
     * Stream new information into the brain.
     *
     * @param string $string The string of information to feed the brain.
     *
     * @throws \Axiom\Rivescript\Exceptions\ParseException
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     * @return void
     */
    public function stream(string $string): void
    {
        $this->openStream();
        $this->writeToMemory($string);
        $this->processInformation();
        $this->sortReplies();
        $this->closeStream();
    }

    /**
     * Process new information in the
     * stream.
     *
     * @throws \Axiom\Rivescript\Exceptions\ParseException
     * @return void
     */
    private function processInformation(): void
    {
        $stream = $this->getStream();
        if (is_resource($stream)) {
            rewind($stream);

            $code = '';
            while (!feof($stream)) {
                $code .= fgets($stream);
            }

            $this->parser->parse(
                filename: "stream",
                code: $code
            );
        }
    }

}
