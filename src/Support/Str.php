<?php

namespace Vulcan\Rivescript\Support;

class Str
{
    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param  String  $string
     * @return String
     */
    public static function removeWhitespace($string)
    {
        return preg_replace('/[\pC\pZ]+/u', ' ', trim($string));
    }

    /**
     * Determine if string starts with the supplied needle.
     *
     * @param  String  $haystack
     * @param  String  $needle
     * @return Boolean
     */
    public static function startsWith($haystack, $needle)
    {
        return $needle === '' or mb_strrpos($haystack, $needle, -mb_strlen($haystack)) !== false;
    }

    /**
     * Determine if string ends with the supplied needle.
     *
     * @param  String  $haystack
     * @param  String  $needle
     * @return Boolean
     */
    public static function endsWith($haystack, $needle)
    {
        return $needle === '' or (($temp = mb_strlen($haystack) - mb_strlen($needle)) >= 0 and mb_strpos($haystack, $needle, $temp) !== false);
    }
}
