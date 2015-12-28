<?php
namespace Vulcan\Rivescript;

class Rivescript extends Utility
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * Create a new Rivescript instance.
     *
     * @param Parser  $parser
     */
    public function __construct(Parser $parser)
    {
        parent::__construct();

        $this->parser = $parser;
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
        return $this->parser->process($file);
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
}
