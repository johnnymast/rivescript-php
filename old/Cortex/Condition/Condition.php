<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Condition;

use Axiom\Rivescript\Traits\Regex;

/**
 * ConditionCommand class
 *
 * The ConditionCommand class is the base class for all
 * conditions in this directory.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\ConditionCommand
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Condition
{
    use Regex;
}
