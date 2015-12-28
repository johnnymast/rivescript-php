<?php
namespace Vulcan\Rivescript;

use Monolog\Logger;
use Monolog\Handlers\StreamHandler;

class Utility()
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Create a new Logger instance.
     *
     * @param Parser  $parser
     */
    public function __construct()
    {
        $this->logger = new Logger('rivescript');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/rivescript.log', Logger::DEBUG));
    }

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

    protected function startsWith($haystack, $needle)
    {
        return $needle === '' or strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    protected function endsWith($haystack, $needle)
    {
        return $needle === '' or (($temp = strlen($haystack) - strlen($needle)) >= 0 and strpos($haystack, $needle, $temp) !== false);
    }
}
