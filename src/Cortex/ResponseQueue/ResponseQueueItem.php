<?php

/**
 * This class represents one item on the ResponseQueue.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     ResponseQueue
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\ResponseQueue;

/**
 * Class ResponseQueueItem
 * @package Axiom\Rivescript\Cortex\ResponseQueue
 */
class ResponseQueueItem
{

    /**
     * The command prefix.
     *
     * @var string
     */
    public $command = '';

    /**
     * The response type.
     *
     * @var string
     */
    public $type = 'atomic';

    /**
     * The sort order of the response.
     *
     * @var int
     */
    public $order = 0;

    /**
     * ResponseQueueItem constructor.
     *
     * @param  string  $command  The command prefix.
     * @param  string  $type     The type of response.
     * @param  int     $order    The order of the response.
     */
    public function __construct(string $command, string $type, int $order = 0)
    {
        $this->command = $command;
        $this->type = $type;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }
}