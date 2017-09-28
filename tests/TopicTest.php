<?php

namespace Tests;

class TopicTest extends ResponseTest
{
    public function testSwitchingTopics()
    {
        $response = $this->rivescript->reply('I hate you!');

        $this->assertEquals('Well that\'s mean. I\'m not talking again until you say you\'re sorry.', $response);
        $this->assertTrue(synapse()->memory->shortTerm()->has('topic'));
        $this->assertEquals(synapse()->memory->shortTerm()->get('topic'), 'sorry');

        $response = $this->rivescript->reply('No! I really mean it!');

        $this->assertEquals('Say you\'re sorry!', $response);
        $this->assertTrue(synapse()->memory->shortTerm()->has('topic'));
        $this->assertEquals(synapse()->memory->shortTerm()->get('topic'), 'sorry');

        $response = $this->rivescript->reply('sorry');

        $this->assertEquals('Alright, I\'ll forgive you.', $response);
        $this->assertTrue(synapse()->memory->shortTerm()->has('topic'));
        $this->assertEquals(synapse()->memory->shortTerm()->get('topic'), 'random');
    }
}
