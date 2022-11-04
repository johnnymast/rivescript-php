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
 * This class contains a set of helper functions.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Utils
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Misc
{
    /**
     * Create a string PDO style/
     *
     * @param string        $msg  The message to write.
     * @param array<string> $args The arguments for the message.
     *
     * @return string
     */
    public static function formatString(string $msg, array $args = []): string
    {
        $search = $replace = [];

        if (is_array($args) === true && count($args) > 0) {
            foreach ($args as $key => $value) {
                $search [] = ":{$key}";
                $replace [] = $value;
            }

            $msg = str_replace($search, $replace, $msg);
        }

        return $msg;
    }
}