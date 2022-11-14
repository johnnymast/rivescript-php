<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Utils;

/**
 * Utils class
 *
 * This class contains utility functions.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Utils
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Utils
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
}