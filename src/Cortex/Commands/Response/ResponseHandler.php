<?php

namespace Axiom\Rivescript\Cortex\Commands\Response;

use Axiom\Rivescript\Cortex\Commands\ResponseAbstract;

/**
 * ResponseHandler interface
 *
 * Description:
 *
 * This class will dictate the rules for all response types
 * to make sure they detect and parse the information in the same
 * way all across the board.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#RESPONSE
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\TriggerCommand
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
interface ResponseHandler
{
    /**
     * Detect if the type of response matches the class implementing
     * these methods. True will be returned if the response type is
     * detected. False will be returned if types don't match.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseAbstract $response The parsed response.
     *
     * @return bool|string
     */
    public function detect(ResponseAbstract $response): bool|string;

    /**
     * Return the type of
     * trigger.
     *
     * @return string
     */
    public function getType(): string;
}