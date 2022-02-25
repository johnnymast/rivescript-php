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

use Axiom\Rivescript\Cortex\ContentLoader\ContentLoader;
use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Output;
use Axiom\Rivescript\Traits\Tags;

/**
 * Rivescript class
 *
 * The entry point for using the interpreter.
 *
 * PHP version 7.4 and higher.
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
    use Tags;

    public const VERBOSITY_NORMAL = 0;
    public const VERBOSITY_VERBOSE = 1;
    public const VERBOSITY_VERY_VERBOSE = 2;
    public const VERBOSITY_DEBUG = 3;

    public $onSay = null;
    public $onWarn = null;
    public $onDebug = null;

    /**
     * A recursion limit before an attempt to
     * fetch a reply will be abandoned.
     *
     * @var int
     */
    public int $depth = 50;

    /**
     * Error messages.
     *
     * @var array|string[]
     */
    public array $errors = [
        "replyNotMatched" => "ERR: No Reply Matched",
        "replyNotFound" => "ERR: No Reply Found",
        "objectNotFound" => "[ERR: Object Not Found]",
        "deepRecursion" => "ERR: Deep Recursion Detected"
    ];

    /**
     * Flag to indicating if utf8
     * modes is enabled.
     *
     * @var bool
     */
    protected bool $utf8 = false;

    /**
     * Flag to indicate debug mode
     * is enabled or not.
     *
     * @var bool
     */
    public bool $debug = false;

    /**
     * This is the user identification.
     *
     * @var string
     */
    private string $client_id = 'local-user';

    /**
     * Create a new Rivescript instance.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct()
    {
        parent::__construct();

        include __DIR__ . '/bootstrap.php';

        synapse()->brain->setMaster($this);
        synapse()->rivescript = $this;

//        $this->setClientId($this->client_id);
        $this->registerTags();
    }

    /**
     * Initialize the Tags
     *
     * @return void
     */
    private function registerTags(): void
    {
        synapse()->tags->each(
            function ($tag) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagInstance = new $class();

                $tagInfo = $tagInstance->getTagName();
                if (is_array($tagInfo)) {
                    foreach ($tagInfo as $tagName) {
                        synapse()->memory->tags()->put($tagName, $tagInstance);
                    }
                } else {
                    synapse()->memory->tags()->put($tagInfo, $tagInstance);
                }
            }
        );
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
     * If you profile a directory with rivescript documents make sure they are
     * all interpretable Rivescript will throw syntax errors while trying to
     * parse those files.
     *
     * @param array<string>|string $info The files to read
     *
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
     * @return void
     */
    public function stream(string $string): void
    {
        fseek($this->getStream(), 0, SEEK_SET);
        rewind($this->getStream());

        $this->writeToMemory($string);
        $this->processInformation();
    }

    /**
     * Process new information in the
     * stream.
     *
     * @return void
     */
    private function processInformation(): void
    {
        synapse()->memory->local()->put('concat', 'none');
        synapse()->brain->teach($this->getStream());
    }

    /**
     * Set the client id.
     *
     * @param string $client_id The client id for this user.
     *
     * @return void
     */
    public function setClientId(string $client_id = 'local-user'): void
    {
        $this->client_id = $client_id;
    }

    /**
     * Return the client id.
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * Set user variables.
     *
     * @param string $name  The name of the variable.
     * @param string $value The value of the variable.
     *
     * @return void
     */
    public function set_uservar(string $name, string $value): void
    {
        synapse()->memory->user($this->client_id)->put($name, $value);
    }

    /**
     * Get user variable.
     *
     * @param string $name The name of the variable.
     *
     * @return mixed
     */
    public function get_uservar(string $name)
    {
        return synapse()->memory->user($this->client_id)->get($name) ?? "undefined";
    }

    /**
     * Log a message to say.
     *
     * @param string $message   The message to print out.
     * @param array  $args      (format) arguments for the message.
     * @param int    $verbosity The verbosity level of the message.
     *
     * @return void
     */
    public function say(string $message, array $args = [], int $verbosity = Rivescript::VERBOSITY_NORMAL): void
    {

        $message = $this->formatString($message, $args);

        if ($this->onSay) {
            call_user_func($this->onSay, $message, $verbosity);
        }
    }

    /**
     * Write a debug message.
     *
     * @param string $message   The message to print out.
     * @param array  $args      (format) arguments for the message.
     * @param int    $verbosity The verbosity level of the message.
     *
     * @return void
     */
    public function warn(string $message, array $args = [], int $verbosity = Rivescript::VERBOSITY_DEBUG): void
    {
        $message = "[WARNING]: " . $this->formatString($message, $args);

        if ($this->onWarn) {
            call_user_func($this->onWarn, $message, $verbosity);
        }
    }

    /**
     * Write a warning.
     *
     * @param string $message   The message to print out.
     * @param array  $args      (format) arguments for the message.
     * @param int    $verbosity The verbosity level of the message.
     *
     * @return void
     */
    public function debug(string $message, array $args = [], int $verbosity = Rivescript::VERBOSITY_NORMAL): void
    {
        $message = "[DEBUG]: " . $this->formatString($message, $args);

        if ($this->onDebug) {
            call_user_func($this->onDebug, $message, $verbosity);
        }
    }


    /**
     * Create a string PDO style/
     *
     * @param string $msg  The message to write.
     * @param array  $args The arguments for the message.
     *
     * @return string
     */
    private function formatString(string $msg, array $args = []): string
    {
        $search = [];
        $replace = [];

        if (is_array($args) === true && count($args) > 0) {
            foreach ($args as $key => $value) {
                $search [] = ":{$key}";
                $replace [] = $value;
            }

            $msg = str_replace($search, $replace, $msg);
        }

        return $msg;
    }

    /**
     * Enable debug mode.
     *
     * @param bool $enabled Enable true/false.
     *
     * @return void
     */
    public function enableDebugMode(bool $enabled = true): void
    {
        synapse()->memory->global()->put('debug', true);
    }

    public function utf8(bool $status = false)
    {
        $this->utf8 = $status;
    }

    public function isUtf8Enabled(): bool
    {
        return ($this->utf8 === true);
    }

    /**
     * Make the client respond to a message.
     *
     * @param string      $msg   The message the client has to process and respond to.
     * @param string      $user  The user id.
     * @param string|null $scope Not used at this point.
     *
     * @return string
     */
    public function reply(string $msg, string $user = 'local-user', string $scope = null): string
    {

        // FIXME: Must be $user, $message, Sscope
        //    $msg = $this->stripNasties($msg, "");
        synapse()->rivescript->say("Asked to reply to :user :msg", ['user' => $user, 'msg' => $msg]);


        $input = new Input($msg, $user);
        $output = new Output();

        synapse()->input = $input;

        $output = $output->process();

        if (empty($output)) {
            $output = $this->errors['replyNotMatched'];
        }

        synapse()->memory->inputs()->push($msg);
        synapse()->memory->replies()->push($output);

        return $output;
    }
}
