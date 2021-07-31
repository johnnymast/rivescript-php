<?php

/**
 * Test the GreaterOrEqualThan class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Conditions;

use Tests\ResponseTest;

class GreaterOrEqualThan extends ResponseTest
{
    public function testSuccessGreaterOrEqualThanGreaterCondition()
    {
        $expected = "Yes 50 i greater than 40";
        $actual = $this->rivescript->reply('Would you think 50 is less or equal to 40');

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessGreaterOrEqualThanEqualsCondition()
    {
        $expected = "in fact 40 equals 40";
        $actual = $this->rivescript->reply("Would you think 40 is less or equal to 40");

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessGreaterOrEqualThanWithStarGreaterSymbolCondition()
    {
        $expected = "in fact 57 is greater then 56";
        $actual = $this->rivescript->reply('Would you think 57 is less or equal to 56');

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessGreaterOrEqualThanWithStarEqualsSymbolCondition()
    {
        $expected = "in fact 59 is equal to 59";
        $actual = $this->rivescript->reply('Would you think 59 is less or equal to 59');

        $this->assertEquals($expected, $actual);
    }

    public function testFailingGreaterOrEqualThanGreaterCondition()
    {
        $expected = "In fact you are wrong its the other way around it is 79 over 21.";
        $actual = $this->rivescript->reply("do you think 21 is greater then 79");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingGreaterOrEqualThanWithStarSymbolCondition()
    {
        $expected = "No that is incorrect. 200 is not greater number than 201.";
        $actual = $this->rivescript->reply("final question 200 is greater than or equal to 201");

        $this->assertEquals($expected, $actual);
    }
}