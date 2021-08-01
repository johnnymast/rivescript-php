<?php

/**
 * Test the LessOrEqualThanTest class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Responses\Condition;

use Tests\ResponseTest;

class LessOrEqualThanTest extends ResponseTest
{
    public function testSuccessLessOrEqualThanEqualsCondition()
    {
        $expected = "In fact 2 equals 2";
        $actual = $this->rivescript->reply("Would you think 2 is less or equal to 2");

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessLessOrEqualThanLessCondition()
    {
        $expected = "In fact 1 is smaller than 2.";
        $actual = $this->rivescript->reply("Would you think 1 is less or equal to 2");

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessLessOrEqualThanWithStarEqualsSymbolCondition()
    {
        $expected = "Yes that is correct. In fact 34 is equals 34.";
        $actual = $this->rivescript->reply("Would you think 34 is less or equal than 34");

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessLessOrEqualThanWithStarLesSymbolCondition()
    {
        $expected = "Yes that is correct. 29 is less than 34.";
        $actual = $this->rivescript->reply('Would you think 29 is less than 34');

        $this->assertEquals($expected, $actual);
    }

    public function testFailingLessOrEqualThanEqualsCondition()
    {
        $expected = "No that is incorrect. 16 is the greater number.";
        $actual = $this->rivescript->reply('Do you think 16 is less than 15');

        $this->assertEquals($expected, $actual);
    }

    public function testFailingLessOrEqualThanWithStarSymbolLessCondition()
    {
        $expected = "No that is incorrect. 20 is the greater number.";
        $actual = $this->rivescript->reply('Do you think 20 is less than 19');

        $this->assertEquals($expected, $actual);
    }
}