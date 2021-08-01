<?php

/**
 * Test the ResponseQueueItem class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     ResponseQueue
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\ResponseQueue;

use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem;
use Tests\ResponseTest;

class ResponseQueueItemTest extends ResponseTest
{
    public function testConstructorSetsValues()
    {
        $command = '* some condition.';
        $type = 'sometype';
        $order = 10;

        $queueItem = new ResponseQueueItem($command, $type, $order);

        $expected = $command;
        $actual = $queueItem->command;
        $this->assertEquals($expected, $actual);

        $expected = $type;
        $actual = $queueItem->type;
        $this->assertEquals($expected, $actual);

        $expected = $order;
        $actual = $queueItem->order;
        $this->assertEquals($expected, $actual);
    }
}