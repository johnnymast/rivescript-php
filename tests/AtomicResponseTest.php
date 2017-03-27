<?php

namespace Tests;

use Vulcan\Rivescript\Utility;

class AtomicResponseTest extends ResponseTest
{
    public function testAtomicReply()
    {
        $response = $this->rivescript->reply('Hello Bot');

        $this->assertEquals('Hello human.', $response);
    }

    public function testAtomicReplyWithVariable()
    {
        $response = $this->rivescript->reply('what is your name?');

        $this->assertEquals('You can call me Rivescript Test Bot.', $response);
    }
}
