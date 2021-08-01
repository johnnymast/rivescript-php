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

class BotTest extends ResponseTest
{
    public function testBotVariable()
    {
        $expected = "You can call me Beta.";
        $actual =  $this->rivescript->reply('what is your name');
        $this->assertEquals($expected, $actual);
    }
}
