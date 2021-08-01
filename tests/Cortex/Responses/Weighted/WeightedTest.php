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

class WeightedTest extends ResponseTest
{
    public function testCorrectWeightIsPrioritized()
    {
        $expected = "i am 2 kilo";
        $actual = $this->rivescript->reply('weight test');

        $this->assertEquals($expected, $actual);
    }
}