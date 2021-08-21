<?php

/**
 * This file handles the "Continue" responses.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Responses
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Responses;

use Axiom\Rivescript\Contracts\Response as ResponseContract;

/**
 * Class Condition
 */
class ContinueResponse extends Response implements ResponseContract
{

    /**
     * Handle Continue responses.
     *
     * @return false|mixed
     */
    public function parse()
    {
        if ($this->responseQueueItem()->getCommand() == '^') {
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
        return 'continue';
    }
}
