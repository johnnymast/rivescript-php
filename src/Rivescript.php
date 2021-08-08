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

use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Output;

/**
 * The main Rivescript client.
 */
class Rivescript
{
    /**
     * Create a new Rivescript instance.
     */
    public function __construct()
    {
        include 'bootstrap.php';
    }

    /**
     * Load RiveScript documents from files.
     *
     * @param  array|string  $files
     */
    public function load($files)
    {
        $files = (!is_array($files)) ? (array)$files : $files;

        foreach ($files as $file) {
            synapse()->brain->teach($file);
        }
    }

    /**
     * Make the client respond to a message.
     *
     * @param  string  $message  The message the client has to process and respond to.
     * @param  int     $user     The user id.
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

//        var_dump("ADDING {$output}\n");
        return $output;
    }
}
