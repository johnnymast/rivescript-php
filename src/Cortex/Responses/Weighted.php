<?php

/**
 * This file parses the weighted responses and updates the
 * order of this response in the response queue.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Responses
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Responses;

use Axiom\Rivescript\Contracts\Response as ResponseContract;

/**
 * Class Weighted
 */
class Weighted extends Response implements ResponseContract
{

    /**
     * Parse the Weighted response.
     *
     * @return bool|string
     */
    public function parse()
    {
        if ($this->responseQueueItem()->getCommand() == '-') {
            $source = $this->source();
            $pattern = '/{weight=([0-9]+)}/';

            if ($this->matchesPattern($pattern, $source)) {
                $matches = $this->getMatchesFromPattern($pattern, $source)[0];

                if (isset($matches[0])) {
                    $this->responseQueueItem->order += (int)$matches[1];
                    $source = str_replace($matches[0], '', $source);
                }
            }
            return $source;
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
        return 'weighted';
    }
}
