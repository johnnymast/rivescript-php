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

use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;
use Tests\ResponseTest;

class ResponseQueueTest extends ResponseTest
{
    public function setUp()
    {
        parent::setUp();

        synapse()->input = new Input("+ my favorite topic", 0);
    }

    public function testWeightedResponses()
    {

        $response1 = new Node("- my weight is{weight=40} 40 kilo's", 0);
        $response2 = new Node("- my weight is{weight=200} 200 kilo's", 0);

        $queue = new ResponseQueue();
        $queue->attach($response1);
        $queue->attach($response2);

        $expected = "my weight is 200 kilo's";
        $actual = $queue->process();

        $this->assertEquals($expected, $actual);
    }

    public function testAtomicResponses()
    {
        $response1 = new Node("- i think it is PHPStorm?", 0);

        $queue = new ResponseQueue();
        $queue->attach($response1);

        $expected = "i think it is PHPStorm?";
        $actual = $queue->process();

        $this->assertEquals($expected, $actual);
    }
}