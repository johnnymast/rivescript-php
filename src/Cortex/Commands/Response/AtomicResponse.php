<?php

namespace Axiom\Rivescript\Cortex\Commands\Response;

use Axiom\Rivescript\Cortex\Attributes\FindResponse;
use Axiom\Rivescript\Cortex\Commands\ResponseCommand;

/**
 * AtomicResponse class
 *
 * Description:
 *
 * This class will detect and handle the definition of the "Atomic response"
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Atomic-Response
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\ResponseCommand
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class AtomicResponse implements ResponseHandler
{
    /**
     * Detect if the type of response matches the class implementing
     * these methods. True will be returned if the response type is
     * detected. False will be returned if types don't match.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseCommand $response
     *
     * @return bool|string
     */
    #[FindResponse]
    public function detect(ResponseCommand $response): bool|string
    {
        if ($response->getNode()->getCommand() == '-') {
            return $this->getType();
        }

        return false;
    }

    /**
     * This function will indicate the type
     * of response. This will be store for login
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