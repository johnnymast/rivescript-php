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
}