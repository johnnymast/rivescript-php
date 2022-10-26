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

use Axiom\Rivescript\ContentLoader\ContentLoader;
use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Output;
use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Events\EventEmitter;
use Axiom\Rivescript\SessionManager\SessionManagerInterface;
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
class Rivescript extends ContentLoader
{
    use EventEmitter;

    /**
     * This is the user identification.
     *
     * @var string
     */
    private string $client_id = 'local-user';

    /**
     * Create a new Rivescript instance.
     *
     * You can set the options by using named options.
     *
     * ```php
     * $rivescript-cli = new RiveScript(debug=true, utf8=true);
     * ```
     *
     * @param bool                     $utf8            Enable utf8 mode true/false
     * @param bool                     $debug           Enable utf8 debug true/false
     * @param bool                     $strict          Enable utf8 strict true/false
     * @param int                      $depth           Max recursion depth.
     * @param string                   $logfile         Use this logfile.
     * @param ?SessionManagerInterface $session_manager pass a customSessionManager.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct(
        public bool                     $utf8 = false,
        public bool                     $debug = false,
        public bool                     $strict = true,
        public int                      $depth = 25,
        public string                   $logfile = '',
        public ?SessionManagerInterface $session_manager = null
    )
    {
        parent::__construct();

        include __DIR__ . '/bootstrap.php';

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
     * @return void
     */
    public function stream(string $string): void
    {
        fseek($this->getStream(), 0);
        rewind($this->getStream());

        $this->writeToMemory($string);
        $this->processInformation();
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

        synapse()->memory->inputs()->push($msg);

        $output = (new Output())
            ->processInput();

        synapse()->memory->replies()->push($output);

        return $output;
    }
}
