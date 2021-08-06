<?php

/**
 * A collection of string helpers.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Support
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Support;

use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;


/**
 * Class Logger
 */
class Logger
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Create a new Logger instance.
     */
    public function __construct()
    {
        $this->logger = new Monolog('rivescript');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../../logs/'.date('m-d-y').'.log', Monolog::DEBUG));
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param  string  $message  The log message
     * @param  array   $context  The log context
     *
     * @return bool Whether the record has been processed
     */
    public function debug(string $message, array $context = []): bool
    {
        return $this->logger->addDebug($message, $context);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string  $message  The log message
     * @param  array   $context  The log context
     *
     * @return bool Whether the record has been processed
     */
    public function warning($message, array $context = []): bool
    {
        return $this->logger->addWarning($message, $context);
    }
}
