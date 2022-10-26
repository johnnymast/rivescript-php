<?php

namespace Tests;

class RedirectTest extends ResponseTest
{
    // FIXME: This is not a redirect command
    public function testRedirectCommand()
    {
        $response = $this->rivescript->reply('hi there');

        $this->assertEquals('Hello human.', $response);
    }
}
