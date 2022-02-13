<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

/**
 * Rivescript class
 *
 * The entry point for using the interpreter.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class TestCaseFilter extends \FilterIterator
{
    public function accept()
    {
        if (preg_match('/.yml/', $this->current())) {
            return true;
        } else {
            return false;
        }
    }
}