<?php

namespace Axiom\Rivescript\Cortex\Commands\Response\Detectors;

use Axiom\Rivescript\Cortex\Commands\ResponseCommand;

/**
 * ResponseDetectorInterface interface
 *
 * Description:
 *
 * This interface makes sure all response detectors respond the same way.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#RESPONSE
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\TriggerCommand\Detectors
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface ResponseDetectorInterface
{
    /**
     * The detect command will detect traits that the triggers will have.
     * If a trait is detected the information will \ed inside the
     * trigger.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseCommand $response The parsed reponse.
     *
     * @return void
     */
    public function detect(ResponseCommand $response): void;
}