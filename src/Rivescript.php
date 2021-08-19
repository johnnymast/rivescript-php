<?php

/**
 * Bootstrap the Rivescript client.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Client
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript;

use Axiom\Rivescript\Cortex\ContentLoader\ContentLoader;
use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Output;

/**
 * The main Rivescript client.
 */
class Rivescript extends ContentLoader
{
    /**
     * Create a new Rivescript instance.
     *
     * @throws \Axiom\Rivescript\Exceptions\ContentLoadingException
     */
    public function __construct()
    {
        parent::__construct();

        include __DIR__ . '/bootstrap.php';
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
     * all interpretable rivescript will throw syntax errors while trying to
     * parse those files.
     *
     * @param array<string>|string $info The files to read
     *
     * @return void
     */
    public function load($info)
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
    public function stream(string $string)
    {
        $this->writeToMemory($string);
        $this->processInformation();
    }

    /**
     * Process new information in the
     * stream.
     *
     * @return void
     */
    private function processInformation()
    {
        synapse()->brain->teach($this->getStream());
    }

    /**
     * Make the client respond to a message.
     *
     * @param string $message The message the client has to process and respond to.
     * @param string $user    The user id.
     *
     * @return string
     */
    public function reply(string $message, string $user = 'local-user'): string
    {
        $input = new Input($message, $user);
        $output = new Output($input);

        synapse()->input = $input;

        $output = $output->process();

        synapse()->memory->inputs()->push($message);
        synapse()->memory->replies()->push($output);

        return $output;
    }
}
