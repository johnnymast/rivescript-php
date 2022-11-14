<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript;

use AllowDynamicProperties;
use Axiom\Rivescript\ContentLoader\ContentLoader;
use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Output;
use Axiom\Rivescript\Cortex\Topic;
use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Events\EventEmitter;
use Axiom\Rivescript\ObjectMacros\ObjectMacrosManager;
use Axiom\Rivescript\Sessions\MemorySessionManagerManager;
use Axiom\Rivescript\Sessions\SessionInterface;
use Axiom\Rivescript\Utils\Misc;

/**
 * Rivescript class
 *
 * The entry point for using the interpreter.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
#[AllowDynamicProperties] class Rivescript extends ContentLoader
{
    use EventEmitter;

    /**
     * Return the Object Macro Manager.
     *
     * @var \Axiom\Rivescript\ObjectMacros\ObjectMacrosManager
     */
    private ObjectMacrosManager $macroManager;

    /**
     * Force-lowercase triggers true/false.
     *
     * @var bool
     */
    public bool $forceCase = true;

    /**
     * Enable strict mode true/false
     *
     * @var bool
     */
    public bool $strict = true;
    /**
     * Enable utf8 mode true/false
     *
     * @var bool
     */
    public bool $utf8 = false;

    /**
     * Enable utf8 debug true/false
     *
     * @var bool
     */
    public bool $debug = false;

    /**
     * The default concatenation mode.
     *
     * @var string
     */
    public string $concat = "none";

    /**
     * Max recursion depth.
     *
     * @var int
     */
    public int $depth = 25;

    /**
     * The actual rivescript parser.
     *
     * @var \Axiom\Rivescript\Parser
     */
    protected Parser $parser;

    public string $logfile = '';

    /**
     * Create a new Rivescript instance.
     *
     * You can set the options by using named options.
     *
     * ```php
     * $rivescript-cli = new RiveScript(debug=true, utf8=true);
     * ```
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct(

        /**
         * Define the options for the Rivescript instance.
         */
        public array             $options = [],

        /**
         * This Session Manager will be used to store user variables.
         *
         * @var \Axiom\Rivescript\SessionManager\SessionInterface
         */
        public ?SessionInterface $sessionManager = null,
    )
    {
        parent::__construct();

        include __DIR__ . '/bootstrap.php';

        $this->macroManager = new ObjectMacrosManager();

        $this->concat = $this->options['concat'] ?? $this->concat;
        $this->debug = $this->options['debug'] ?? $this->debug;
        $this->depth = $this->options['depth'] ?? $this->depth;
        $this->utf8 = $this->options['utf8'] ?? $this->utf8;
        $this->strict = $this->options['strict'] ?? $this->strict;
        $this->forceCase = $this->options['forceCase'] ?? $this->forceCase;

        if (!$sessionManager) {
            $this->sessionManager = new MemorySessionManagerManager();
        }

        $this->parser = new Parser($this);

        /**
         * Set default global variables. These
         * can be overwritten by the script.
         */
        synapse()->rivescript = $this;
        synapse()->memory->global()->put('depth', 25);
        synapse()->memory->global()->put('debug', false);
        synapse()->memory->global()->put('verbose', false);
    }

    /**
     * Return the macro manager.
     *
     * @return \Axiom\Rivescript\ObjectMacros\ObjectMacrosManager
     */
    public function getObjectMacroManager(): ObjectMacrosManager
    {
        return $this->macroManager;
    }

    /**
     * Load Rivescript interpretable content.
     * Into the Interpreter.
     *
     * Please note: This supports
     *
     * - Directory path to Rivescript interpretable files.
     * - Array of absolute paths to Rivescript interpretable files
     * - Absolute string containing path to Rivescript interpretable file.
     * - A stream of text with Rivescript interpretable script.
     *
     * Please note 2:
     *
     * If you profile a directory with rivescript-cli documents make sure they are
     * all interpretable Rivescript will throw syntax errors while trying to
     * parse those files.
     *
     * @param array<string>|string $info The files to read
     *
     * @throws \Axiom\Rivescript\Exceptions\ParseException
     * @return void
     */
    public function load($info): void
    {
        parent::load($info);
        $this->processInformation();
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
     * Sort the replies.
     *
     * @return void
     */
    public function sortReplies(): void
    {
        synapse()->brain->topics()->each(fn(Topic $topic) => $topic->sortTriggers());
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
        synapse()->memory->local()->put('concat', 'none');
        synapse()->brain->teach($this->getStream());
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
        //$this->sessionManager->set($user, )

        if ($name == "topic" && $this->forceCase) {
            $value = strtolower($value);
        }


        $fields = [$name => $value];
        $this->sessionManager->set($user, $fields);
    }

    /**
     * This function can be called in two ways::
     *
     * # Set a dict of variables for a single user.
     * rs.set_uservars(username, vars)
     *
     * # Set a nested dict of variables for many users.
     * rs.set_uservars(many_vars)
     *
     * In the first syntax, ``vars`` is a simple dict of key/value string
     * pairs. In the second syntax, ``many_vars`` is a structure like this::
     * {
     * "username1": {
     * "key": "value",
     * },
     * "username2": {
     * "key": "value",
     * },
     * }
     * This way you can export *all* user variables via ``get_uservars()``
     * and then re-import them all at once, instead of setting them once per
     * user.
     *
     * :param optional str user: The user ID to set many variables for.
     * Skip this parameter to set many variables for many users instead.
     * :param dict data: The dictionary of key/value pairs for user variables,
     * or else a dict of dicts mapping usernames to key/value pairs.
     * This may raise a ``TypeError`` exception if you pass it invalid data
     * types. Note that only the standard ``dict`` type is accepted, but not
     * variants like ``OrderedDict``, so if you have a dict-like type you
     * should cast it to ``dict`` first.
     *
     * @param string               $username The user to set the variables for.
     * @param array<string, mixed> $data     The data to set for the user.
     *
     * @return void many variables for a user, or set many variables for many users.
     */
    public function setUserVars(string $username, array $data): void
    {
        $this->sessionManager->set($username, $data);
    }

    /**
     * Get a variable about a user.
     *
     * @param string $user The user ID to look up a variable for.
     * @param string $name The name of the variable to get.
     *
     * @return mixed  The user variable, or ``None`` or ``"undefined"``:
     * If the user has no data at all, this returns ``None``.
     * If the user doesn't have this variable set, this returns the
     * string ``"undefined"``.
     * Otherwise this returns the string value of the variable.
     */
    public function getUserVar(string $user, string $name): mixed
    {
        return $this->sessionManager->get($user, $name);
    }

    /**
     * Get all variables about a user (or all users).
     *
     * @param string|null $user The user ID to retrieve all variables for.
     *                          If not passed, this function will return all data for all users.
     *
     * @return mixed dict: All the user variables.
     * If a ``user`` was passed, this is a ``dict`` of key/value pairs
     * of that user's variables. If the user doesn't exist in memory,
     * this returns ``None``.
     * Otherwise, this returns a ``dict`` of key/value pairs that map
     * user IDs to their variables (a ``dict`` of ``dict``).
     */
    public function getUserVars(string $user = null): mixed
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
    public function freezeUservars(string $user): void
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
     * @return void
     */
    public function thawUservars(string $user, string $action = "thaw"): void
    {
        $this->sessionManager->thaw($user, $action);
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

        $this->emit(EVENT::DEBUG, $message);
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

        $this->emit(EVENT::DEBUG_VERBOSE, $message);
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

        $this->emit(Event::DEBUG_WARNING, $message);
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

        $this->emit(EVENT::DEBUG_ERROR, $message);
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

        $this->emit(EVENT::SAY, $message);
    }

    /**
     * Make the client respond to a message.
     *
     * @param string $msg  The message the client has to process and respond to.
     * @param string $user The user id.
     *
     * @return string
     */
    public function reply(string $msg, string $user = 'local-user'): string
    {

        synapse()->rivescript->say("Asked to reply to :user :msg", ['user' => $user, 'msg' => $msg]);

        synapse()->input = new Input($msg, $user);

        synapse()->memory
            ->inputs()
            ->push($msg);

        $output = (new Output())
            ->processInput();

        synapse()->memory
            ->replies()
            ->push($output);

        return $output;
    }
}
