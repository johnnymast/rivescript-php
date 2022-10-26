<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
 * @since    0.3.0
 */
class Str
{
    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param  string  $string  The string to remove whitespace from.
     *
     * @return string
     */
    public static function removeWhitespace(string $string): string
    {
        return preg_replace('/[\pC\pZ]+/u', ' ', trim($string));
    }

    /**
     * Determine if string starts with the supplied needle.
     *
     * @param  string  $haystack  The containing string.
     * @param  string  $needle    The needle to look for.
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return $needle === '' or mb_strrpos($haystack, $needle, -mb_strlen($haystack)) !== false;
    }

    /**
     * Determine if string ends with the supplied needle.
     *
     * @param  string  $haystack
     * @param  string  $needle
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return $needle === '' or (($temp = mb_strlen($haystack) - mb_strlen($needle)) >= 0 and mb_strpos(
            $haystack,
            $needle,
            $temp
        ) !== false);
    }
}
