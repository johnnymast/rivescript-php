<?php

/**
 * Test the NotEquals class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Response\Weighted;

use Tests\ResponseTest;

class AtomicTest extends ResponseTest
{
    public function testCorrectWeightIsPrioritized()
    {
        $expected = "Hello human.";
        $actual = $this->rivescript->reply('hello bot');

        $this->assertEquals($expected, $actual);
    }
}