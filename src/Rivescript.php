<?php
namespace Vulcan\Rivescript;

class Rivescript
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
        $this->parser = $parser;
    }

    /**
     * Load a RiveScript document from a file.
     *
     * @param array|string  $file
     */
    public function loadFile($file)
    {
        return $this->parser->process($file);
    }
}
