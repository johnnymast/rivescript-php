<?php

namespace Tests;

class RedirectTest extends ResponseTest
{
    public function testRedirectCommand()
    {
        $response = $this->rivescript->reply('hi there');

        $this->assertEquals('Hello human.', $response);
    }
}
