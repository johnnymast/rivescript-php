<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Contracts;

use Axiom\Rivescript\Cortex\Input;

/**
 * TriggerCommand interface
 *
 * The TriggerCommand interface is used by TriggerCommand detectors.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Contracts
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
interface Trigger
{
    /**
     * Parse the trigger.
     *
     * @param string $trigger The trigger to parse.
     * @param Input  $input   Input information.
     *
     * @return bool
     */
    public function parse(string $trigger, Input $input): bool;
}
