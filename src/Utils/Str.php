<?php

/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript\Utils;

use Axiom\Rivescript\Traits\Regex;

/**
 * Str class
 *
 * A collection of string helpers.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Support
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Str
{
    use Regex;

    /**
     * Strip extra whitespace from both ends of the string, and remove
     * line breaks anywhere in the string.
     *
     * @param string $text The text to strip.
     *
     * @return string
     */
    public static function strip(string $text): string
    {
        $text = preg_replace("/^[\s\t]+/", '', $text);
        $text = preg_replace("/[\s\t]+$/", '', $text);
        $text = preg_replace("/[\x0D\x0A]+/", '', $text);
        return $text;
    }

    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param string $string The string to remove whitespace from.
     *
     * @return string
     */
    public static function removeWhitespace(string $string): string
    {
        return preg_replace('/[\pC\pZ]+/u', ' ', trim($string));
    }

    /**
     * finds a match in a string at a given index
     *
     * Usage:
     *
     * $string = "My name is Rive"
     * $match = " "
     * $index = 2
     *
     * return = 7
     *
     * @param string $string The string to search in.
     * @param string $match  The string to match.
     * @param int    $index  The index to search for.
     *
     * @return int
     */
    public static function nIndexOf(string $string, string $match, int $index): int
    {
        return strlen(join($match, array_slice(explode($string, $match), 0, $index + 1)));
    }

    /**
     * Stip special characters out of a string.
     *
     * @param string $string The string to strip.
     * @param bool   $utf8   Whether to allow UTF8 characters.
     *
     * @return string
     */
    public static function stripNasties(string $string, bool $utf8 = false): string
    {
        if ($utf8) {
            // Allow most things in UTF8 mode.
            $string = preg_replace('/[\\<>]+/', "", $string);
            return $string;
        }

        $string = preg_replace('/[^A-Za-z0-9 ]/', "", $string);
        return $string;
    }
}