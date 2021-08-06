<?php

/**
 * This file handles the Atomic responses.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Responses
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Responses;

use Axiom\Rivescript\Contracts\Response as ResponseContract;

/**
 * Class Atomic
 */
class Atomic extends Response implements ResponseContract
{

    /**
     * Handle Atomic response.
     *
     * @return bool|string
     */
    public function parse()
    {
        if ($this->responseQueueItem()->getCommand() == '-') {
            return $this->source();
        }

        return false;
    }

    /**
     * Indicate the type of response this
     * class handles.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'atomic';
    }
}
