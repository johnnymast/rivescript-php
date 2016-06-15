<?php

namespace Vulcan\Rivescript;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Utility
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
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../logs/'.date('m-d-y_h-i-s').'.log', Logger::DEBUG));
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string  $message The log message
     * @param  array  $context The log context
     * @return Boolean  Whether the record has been processed
     */
    public function debug($message, array $context = array())
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
    public function warning($message, array $context = array())
    {
        return $this->logger->addWarning($message, $context);
    }

    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param string  $line
     * @return string
     */
    public function removeWhitespace($line)
    {
        $line = trim($line);
        preg_replace('/( )+/', ' ', $line);

        return $line;
    }

    /**
     * Determine if string starts with the supplied needle.
     *
     * @param string  $haystack
     * @param string  $needle
     * @return bool
     */
    public function startsWith($haystack, $needle)
    {
        return $needle === '' or strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * Determine if string ends with the supplied needle.
     *
     * @param string  $haystack
     * @param string  $needle
     * @return bool
     */
    public function endsWith($haystack, $needle)
    {
        return $needle === '' or (($temp = strlen($haystack) - strlen($needle)) >= 0 and strpos($haystack, $needle, $temp) !== false);
    }
}
