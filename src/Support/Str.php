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

/**
 * Class Str
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
