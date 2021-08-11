<?php

/**
 * Test the {sentence} and <sentence> tag from the Sentence class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Tags;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('tags');

it("will transform sentences between {sentence} and {/sentence} to uppercase first char (Single-Sentence)", function () {
    $expected = "roger this is: First sentence with ucfirst. curly bracket";
    $actual = $this->rivescript->reply("sentence test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between {sentence} and {/sentence} to uppercase first char (Multi-Word)", function () {
    $expected = "roger this is: First sentence with ucfirst.This is the second sentence with ucfirst. curly bracket";
    $actual = $this->rivescript->reply("sentence test 2");

    $this->assertEquals($expected, $actual);
});

it("will transform <sentence> as alias of {sentence}<star>{/sentence} (Single-Sentence)", function () {
    $expected = "roger this is: This is my test. angled bracket";
    $actual = $this->rivescript->reply("sentence test 3 this is my test");

    $this->assertEquals($expected, $actual);
});

it("will return undefined if no * is used to translate <sentence>", function () {
    $expected = "roger this is: undefined. angled bracket";
    $actual = $this->rivescript->reply("sentence test 4");

    $this->assertEquals($expected, $actual);
});