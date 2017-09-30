<?php

namespace Tests;

class SessionTest extends ResponseTest
{
    public function testMemorySession()
    {
        $response = $this->rivescript->reply('What\'s my name?');
        $this->assertEquals('Your name is undefined!', $response);

        $response = $this->rivescript->reply('My name is Kai');
        $this->assertEquals('Nice to meet you!', $response);

        $response = $this->rivescript->reply('What\'s my name?');
        $this->assertEquals('Your name is kai!', $response);
    }
}
