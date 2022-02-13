<?php

/**
 * Test the {@topic} tag from the Topic class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Tests\Cortex\Tags;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('Tags');

it('answers with the tag removed', function () {
    $expected = "Well that's mean. I'm not talking again until you say you're sorry.";
    $actual = $this->rivescript->reply("i hate you");

    $this->assertEquals($expected, $actual);
});

it('forwards the topic to sorry', function () {
    $expected = "Well that's mean. I'm not talking again until you say you're sorry.";
    $actual = $this->rivescript->reply("i hate you");
    $this->assertEquals($expected, $actual);

    $this->assertTrue(synapse()->memory->shortTerm()->has('topic'));
    $this->assertEquals(synapse()->memory->shortTerm()->get('topic'), 'sorry');
});

it('returns to topic random after exiting topic sorry', function () {
    $expected = "Well that's mean. I'm not talking again until you say you're sorry.";
    $actual = $this->rivescript->reply("i hate you");
    $this->assertEquals($expected, $actual);

    $this->assertTrue(synapse()->memory->shortTerm()->has('topic'));
    $this->assertEquals(synapse()->memory->shortTerm()->get('topic'), 'sorry');

    $expected = "Alright, I'll forgive you.";
    $actual = $this->rivescript->reply("sorry");
    $this->assertEquals($expected, $actual);

    $this->assertTrue(synapse()->memory->shortTerm()->has('topic'));
    $this->assertEquals(synapse()->memory->shortTerm()->get('topic'), 'random');
});