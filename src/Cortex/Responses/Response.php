<?php

/**
 * The base class of Responses.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Responses
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Responses;

use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem;
use Axiom\Rivescript\Traits\Regex;

/**
 * Class Response
 */
abstract class Response extends \SplObjectStorage
{
    use Regex;

    /**
     * @var ResponseQueueItem
     */
    protected $responseQueueItem;

    /**
     * @var string
     */
    protected $source = '';

    /**
     * Responses must implement this method to
     * indicate the response type.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Response constructor.
     *
     * @param  string             $source             The response line.
     * @param  ResponseQueueItem  $responseQueueItem  Queue information about the response line.
     */
    public function __construct(string $source, ResponseQueueItem &$responseQueueItem)
    {
        $this->source = $source;
        $this->responseQueueItem = $responseQueueItem;
    }

    /**
     * @return string
     */
    public function source(): string
    {
        return $this->source;
    }

    /**
     * Return the responseQueueItem.
     *
     * @return ResponseQueueItem
     */
    public function responseQueueItem(): ResponseQueueItem
    {
        return $this->responseQueueItem;
    }
}
