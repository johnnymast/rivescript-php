<?php

/**
 * Test the NotEquals class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Response\Condition;

use Tests\ResponseTest;

class NotEqualsTest extends ResponseTest
{
    public function testSuccessExclamationEqualsNotEqualCondition()
    {
        $expected = "yes that is correct, 4 does not equals 2";
        $actual = $this->rivescript->reply('is it correct that 4 not equals 2');

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessNEAlias()
    {
        $expected = "yes that is correct, 5 does not equals 2";
        $actual = $this->rivescript->reply('is it correct that 5 not equals 2');

        $this->assertEquals($expected, $actual);
    }

    public function testSuccessGreaterOrLowerThanSymbolAlias()
    {
        $expected = "yes that is correct, 6 does not equals 1";
        $actual = $this->rivescript->reply('is it correct that 6 not equals 1');

        $this->assertEquals($expected, $actual);
    }

    public function testStarNotEquals()
    {
        $expected = "yes that is correct, 9 does not equal 10";
        $actual = $this->rivescript->reply("what do you think 9 not equals 10");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingExclamationEqualsNotEqualCondition()
    {
        $expected = "Your statement is incorrect 90 does match 90.";
        $actual = $this->rivescript->reply("is it correct that 90 not equals 90");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingNEAlias()
    {
        $expected = "Your statement is incorrect 81 does match 81.";
        $actual = $this->rivescript->reply("is it correct that 81 not equals 81");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingGreaterOrLowerThanSymbolAlias()
    {
        $expected = "Your statement is incorrect 72 does match 72.";
        $actual = $this->rivescript->reply("is it correct that 72 not equals 72");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingStarNotEquals()
    {
        $expected = "Your statement is incorrect 102 does match 102.";
        $actual = $this->rivescript->reply("what do you think is 102 not equals 102");

        $this->assertEquals($expected, $actual);
    }
}