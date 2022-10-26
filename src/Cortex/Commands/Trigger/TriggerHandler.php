<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands\Trigger;

use Axiom\Rivescript\Cortex\Commands\TriggerCommand;

/**
 * TriggerDetector interface
 *
 * Description:
 *
 * This class will dictate the rules for all trigger types
 * to make sure they detect and parse the information in the same
 * way all across the board.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#TRIGGER
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\Trigger
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface TriggerHandler
{
    /**
     * Detect if the type of trigger matches the class implementing
     * these methods. True will be returned if the trigger type is
     * detected. False will be returned if types don't match.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger The parsed trigger.
     *
     * @return bool|string
     */
    public function detect(TriggerCommand $trigger): bool|string;

    /**
     * Return the type of
     * trigger.
     *
     * @return string
     */
    public function getType(): string;
}