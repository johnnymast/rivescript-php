<?php

/**
 * A set of global functions used in the project.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Legacy
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

use Axiom\Rivescript\Support\Str;
use Axiom\Rivescript\Cortex\Synapse;
use Axiom\Rivescript\Support\Logger;

if (!function_exists('synapse')) {
    /**
     * Get the available Synapse instance.
     *
     * @return Synapse
     */
    function synapse(): Synapse
    {
        return Synapse::getInstance();
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variable(s) and end the script.
     *
     * param  mixed  $args  The information to dump.
     *
     * @return void
     */
    function dd()
    {
        array_map(function($x) {
            print_r($x);
            echo "\n";
        }, func_get_args());
        die;
    }
}

if (!function_exists('ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack  The containing string.
     * @param  string  $needle    The needle to look for.
     *
     * @return bool
     */
    function ends_with(string $haystack, string $needle): bool
    {
        return Str::endsWith($haystack, $needle);
    }
}

if (!function_exists('log_debug')) {
    /**
     * Log the message and contextual array as a new debug entry.
     *
     * @param  string   $message  The message to output.
     * @param  array[]  $context  The context for the message.
     *
     * @return bool
     */
    function log_debug(string $message, array $context = []): bool
    {
        $logger = new Logger();

        return $logger->debug($message, $context);
    }
}

if (!function_exists('log_warning')) {
    /**
     * Log the message and contextual array as a new warning entry.
     *
     * @param  string  $message  The message to output.
     * @param  array[]   $context  The context for the message.
     *
     * @return bool
     */
    function log_warning(string $message, array $context = []): bool
    {
        $logger = new Logger();

        return $logger->warning($message, $context);
    }
}

if (!function_exists('remove_whitespace')) {
    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param  string  $line  The line to remove whitespace from.
     *
     * @return string
     */
    function remove_whitespace(string $line): string
    {
        return Str::removeWhitespace($line);
    }
}

if (!function_exists('starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack  The containing string.
     * @param  string  $needle    The needle to look for.
     *
     * @return bool
     */
    function starts_with(string $haystack, string $needle): bool
    {
        return Str::startsWith($haystack, $needle);
    }
}
