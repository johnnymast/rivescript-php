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

class EnvTest extends ResponseTest
{
    public function testEnvTagValue()
    {
        $expected = "The topic is sensation.";
        $actual = $this->rivescript->reply("what is your global topic");
        $this->assertEquals($expected, $actual);
    }
}
