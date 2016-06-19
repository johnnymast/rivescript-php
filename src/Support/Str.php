<?php

namespace Vulcan\Rivescript\Support;

class Str
{
    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @param string  $line
     * @return string
     */
    public static function removeWhitespace($line)
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
    public static function startsWith($haystack, $needle)
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
    public static function endsWith($haystack, $needle)
    {
        return $needle === '' or (($temp = strlen($haystack) - strlen($needle)) >= 0 and strpos($haystack, $needle, $temp) !== false);
    }
}
