<?php
namespace Vulcan\Rivescript;

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

class Rivescript
{
    /**
     * @var Logger
     */
    protected $logger;

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
        $this->logger = new Logger('rivescript');
        $this->parser = $parser;

        $this->logger->pushHandler(new StreamHandler(__DIR__.'/rivescript.log', Logger::DEBUG));
    }

    /*
    |--------------------------------------------------------------------------
    | Debug Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string  $message The log message
     * @param  array  $context The log context
     * @return Boolean  Whether the record has been processed
     */
    protected function debug($message, array $context = array())
    {
        return $this->logger->addDebug($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string  $message The log message
     * @param  array  $context The log context
     * @return Boolean  Whether the record has been processed
     */
    protected function warning($message, array $context = array())
    {
        return $this->logger->addWarning($message, $context);
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
