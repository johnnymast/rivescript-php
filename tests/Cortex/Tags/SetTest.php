<?php

/**
 * Test the Sub tag from the Sub class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Tags;

use Tests\ResponseTest;

class SetTest extends ResponseTest
{
    public function testSetTagValue()
    {
        $name = "settest";
        $this->rivescript->reply("my name is {$name}");

        $expected = "Your name is {$name}!";
        $actual = $this->rivescript->reply("what is my name");
        $this->assertEquals($expected, $actual);
    }
}
