<?php

namespace Tests;

class AtomicResponseTest extends ResponseTest
{
    public function testAtomicReply()
    {
        $response = $this->rivescript->reply('Hello Bot');

        $this->assertEquals('Hello human.', $response);
    }

    public function testAtomicReplyWithGlobalVariable()
    {
        $response = $this->rivescript->reply('what is your global topic');

        $this->assertEquals('The topic is sensation.', $response);
    }

    public function testAtomicReplyWithMissingGlobalVariable()
    {
        $response = $this->rivescript->reply('what is defined under bleep');

        $this->assertEquals('The value defined is undefined.', $response);
    }

    public function testAtomicReplyWithBotVariable()
    {
        $response = $this->rivescript->reply('what is your name');

        $this->assertEquals('You can call me Beta.', $response);
    }

    public function testAtomicReplyWithMissingBotVariable()
    {
        $response = $this->rivescript->reply('what is defined under bleep');

        $this->assertEquals('The value defined is undefined.', $response);
    }

    public function testAtomicReplyWithUserVariable()
    {
        $response = $this->rivescript->reply('My name is Kai');
        $this->assertEquals('Nice to meet you!', $response);

        $response = $this->rivescript->reply('My name is Kai');
        $this->assertEquals('Nice to meet you!', $response);

        $response = $this->rivescript->reply('What\'s my name?');
        $this->assertEquals('Your name is kai!', $response);
    }

    public function testAtomicReplyWithMissingUserVariable()
    {
        $response = $this->rivescript->reply('What\'s my name?');
        $this->assertEquals('Your name is undefined!', $response);
    }
}
