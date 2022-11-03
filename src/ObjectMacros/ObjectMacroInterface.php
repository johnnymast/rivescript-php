<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\ObjectMacros;

/**
 * ObjectMacroInterface interface
 *
 * This interface will make sure all Object Macro plugins
 * will implement the same functions.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  ObjectMacros
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface ObjectMacroInterface
{
    public function getLanguage(): string;

    public function execute(string $code): string;
}