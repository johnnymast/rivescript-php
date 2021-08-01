<?php

/**
 * Test the GreaterThanTest class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Responses\Condition;

use Tests\ResponseTest;

class GreaterThanTest extends ResponseTest
{
    public function testSuccessGreaterThanCondition()
    {
        $expected = "Yes that is correct";
        $actual = $this->rivescript->reply('Do you think 10 is greater then 5');

        $this->assertEquals($expected, $actual);
    }


    public function testSuccessGreaterThanWithStarSymbolCondition()
    {
        $expected = "Yes that is correct";
        $actual = $this->rivescript->reply('Do you think 11 is greater then 10');

        $this->assertEquals($expected, $actual);
    }

    public function testFailingGreaterThanCondition()
    {
        $expected = "No that is incorrect. 15 is the greater number.";
        $actual = $this->rivescript->reply("Do you think 4 is greater then 15");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingGreaterThanWithStarSymbolCondition()
    {
        $expected = "No that is incorrect.";
        $actual = $this->rivescript->reply('Do you think 2 is greater then 10');

        $this->assertEquals($expected, $actual);
    }
}