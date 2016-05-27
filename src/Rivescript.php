<?php

namespace Vulcan\Rivescript;

class Rivescript extends Utility
{
    /**
     * @var Parser
     */
    protected $parser;

    public $tree;

    /**
     * Create a new Rivescript instance.
     *
     * @param Parser  $parser
     */
    public function __construct()
    {
        parent::__construct();

        $this->parser = new Parser;
    }

    /*
    |--------------------------------------------------------------------------
    | Loading and Parsing Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Load a RiveScript document from a file.
     *
     * @param array|string  $file
     */
    public function loadFile($file)
    {
        $this->tree = $this->parser->process($file);
    }

    private function parse($file)
    {
        $tree = $this->parser->process($file);

        // Get all of the "begin" type variables:
        // global, var, sub, person, array...
        foreach($tree['begin'] as $type => $vars) {
            //
        }
    }

    public function reply($user, $message)
    {
        $triggers = $this->tree['topics']['random']['triggers'];

        $found = array_search($message, array_column($triggers, 'trigger'));

        if (is_int($found)) {
            $replies = $triggers[$found]['reply'];

            // echo "\n---\n\n";
            // var_dump($replies);
            // echo "\n---\n\n";

            if (count($replies)) {
                return $replies[array_rand($replies)];
            }
        }

        return 'No response found.';
    }
}
