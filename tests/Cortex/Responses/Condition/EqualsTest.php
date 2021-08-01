<?php

/**
 * Test the Equals class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Conditions
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Responses\Condition;

use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;
use Tests\ResponseTest;

class EqualsTest extends ResponseTest
{
    public function testDoubleEqualSymbols()
    {
        $expected = "yes that is correct, 1 equals 1";
        $actual = $this->rivescript->reply('do you think 1 equals 1');

        $this->assertEquals($expected, $actual);
    }

    public function testEQAlias()
    {
        $expected = "yes that is correct, 2 equals 2";
        $actual = $this->rivescript->reply("do you think 2 equals 2");

        $this->assertEquals($expected, $actual);
    }

    public function testStarEquals()
    {
        $expected = "yes that is correct, 4 equals 4";
        $actual = $this->rivescript->reply("do you think 4 equals 4");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingDoubleEqualSymbol()
    {
        $expected = "No this is not correct";
        $actual = $this->rivescript->reply("do you think 4 equals 2");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingEQAlias()
    {
        $expected = "No this is not correct. 5 equals 5 not 3";
        $actual = $this->rivescript->reply("do you think 5 equals 3");

        $this->assertEquals($expected, $actual);
    }

    public function testStarVariableEquals()
    {
        $expected = "No that is the incorrect answer, my name is Beta";
        $actual = $this->rivescript->reply("is your name Hal");

        $this->assertEquals($expected, $actual);
    }

    public function testFailingBotVariableEquals()
    {
        $expected = "No that is the incorrect answer, my name is Beta";
        $actual = $this->rivescript->reply("is your name Hal");

        $this->assertEquals($expected, $actual);
    }
}