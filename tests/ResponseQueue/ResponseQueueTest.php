<?php

/**
 * Test the ResponseQueue class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     ResponseQueue
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\ResponseQueue;

use Axiom\Rivescript\Cortex\Commands\Topic;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;
use Tests\ResponseTest;

class ResponseQueueTest extends ResponseTest
{

    public function testWeightedResponses()
    {
//        $node = new Node();
//        $topic = new Topic();
//        $queue = $topic->parse();

//        $queue->attach()
    }

    public function testAtomicResponses()
    {
    }
}