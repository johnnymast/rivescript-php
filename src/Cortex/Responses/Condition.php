<?php

/**
 * This file handles the Condition responses.
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
 * @package Axiom\Rivescript\Cortex\Responses
 */
class Condition extends Response implements ResponseContract
{

    /**
     * Handle Conditions, if the source is an Condition
     * then we need to handle the conditions.
     *
     * @return false|mixed
     */
    public function parse()
    {
        if ($this->responseQueueItem()->getCommand() == '*') {
            foreach (synapse()->conditions as $class) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Conditions\\{$class}";
                $class = new $class();

                $result = $class->parse($this->source());

                if ($result !== false) {
                    return $result;
                }
            }
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
        return 'condition';
    }
}
