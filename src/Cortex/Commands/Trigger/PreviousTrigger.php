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

use Axiom\Rivescript\Cortex\Attributes\FindTrigger;
use Axiom\Rivescript\Cortex\Commands\TriggerCommand;
use function Axiom\Rivescript\Cortex\Commands\Trigger\str_starts_with;

/**
 * PreviousTrigger class
 *
 * Description:
 *
 * This class will detect and handle the definition of the "PreviousCommand TriggerCommand"
 *
 * @see      https://www.rivescript.com/wd/RiveScript#PREVIOUS
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
class PreviousTrigger implements TriggerHandler
{
    /**
     * Detect if the type of trigger matches an atomic trigger. True will
     * be returned if detected. False will be returned if not detected.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return bool|string
     */
    #[FindTrigger]
    public function detect(TriggerCommand $trigger): bool|string
    {
        if ($trigger->getNode()->getCommand() == '%') {
            return $this->getType();
        }

        return false;
    }

    /**
     * This function will indicate the type
     * of trigger. This will be store for login
     * and reference inside the global trigger
     * object.
     *
     * @return string
     */
    public function getType(): string
    {
        return self::class;
    }
}
