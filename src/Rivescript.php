<?php

namespace Vulcan\Rivescript;

use Vulcan\Rivescript\Interpreter\Input;
use Vulcan\Rivescript\Interpreter\Output;

class Rivescript
{
    /**
     * @var Synapse
     */
    protected $synapse;

    /**
     * Create a new Rivescript instance.
     */
    public function __construct()
    {
        include('bootstrap.php');
    }

    /**
     * Load RiveScript documents from files.
     *
     * @param  array|string  $files
     */
    public function load($files)
    {
        $files = (! is_array($files)) ? (array) $files : $files;

        foreach ($files as $file) {
            synapse()->brain->teach($file);
        }
    }

    public function reply($message, $user = null)
    {
        $input  = new Input($message, $user);
        $output = new Output($input);

        return $output->process();
    }
}
