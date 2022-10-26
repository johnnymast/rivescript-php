<?php

namespace Axiom\Rivescript\Cortex\Commands\Trigger\Detectors;

use Axiom\Rivescript\Cortex\Commands\TriggerCommand;

/**
 * TriggerDetectorInterface interface
 *
 * Description:
 *
 * This interface makes sure all trigger detectors respond the same way.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#TRIGGER
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\Trigger\Detectors
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface TriggerDetectorInterface
{
    /**
     * The detect command will detect traits that the triggers will have.
     * If a trait is detected the information will be stored inside the
     * trigger.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger The parsed trigger.
     *
     * @return void
     */
    public function detect(TriggerCommand $trigger): void;
}