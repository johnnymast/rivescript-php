<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\ObjectMacros\Macros;

use Axiom\Rivescript\ObjectMacros\ObjectMacroInterface;

/**
 * PHPMacro class
 *
 * This PHPMacro will execute PHP code for the for
 * Object Macro's.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  ObjectMacros\Macros
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class PHPMacro implements ObjectMacroInterface
{
    /**
     *
     */
    private const PHP_INTERPRETER = "php";

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return "PHP";
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public function execute(string $code): string
    {
        $interpreter = self::PHP_INTERPRETER;
        $code = stripslashes($code);
        return shell_exec("{$interpreter} -v {$code}");
    }
}