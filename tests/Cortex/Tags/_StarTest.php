<?php

/**
 * Test the <star> / <start1>-<star9> tag from the Star class.
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

it("will translate one star", function () {
    $expected = "Why do you like programming so much?";
    $actual = $this->rivescript->reply('my favorite thing in the world is programming');

    $this->assertEquals($expected, $actual);
});

it("will translate two stars", function () {
    $expected = "Why would john have told you to say hello?";
    $actual = $this->rivescript->reply('John told me to say hello');

    $this->assertEquals($expected, $actual);
});

it("translates star with multiple words", function () {
    $expected = "Do you think the sky is orange a lot?";
    $actual = $this->rivescript->reply('I think the sky is orange.');

    $this->assertEquals($expected, $actual);
});

it("translates star with wildcard", function () {
    $expected = "Tell me that as a number instead of spelled out like \"twenty\"";
    $actual = $this->rivescript->reply('I am twenty years old');

    $this->assertEquals($expected, $actual);
});

it("translates star with number", function () {
    $expected = "I will remember that you are 20 years old.";
    $actual = $this->rivescript->reply("I am 20 years old");

    $this->assertEquals($expected, $actual);
});

it('works as catch-all for unknown replies', function () {
    $expected = "I'm sorry but I don't understand.";
    $actual = $this->rivescript->reply("foobarbaz123");

    $this->assertEquals($expected, $actual);
});

it("works as catch-all for unknown replies (multi-word)", function () {
    $expected = "I'm sorry but I don't understand.";
    $actual = $this->rivescript->reply("lorem ipsum dolar amit");

    $this->assertEquals($expected, $actual);
});