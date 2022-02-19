<?php

/**
 * Test the \s|\n|\/|\# tag from the SpecialChars.yml class.
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
    ->group('Tags');

it("replaces the \\s tag with a whitespace", function () {
    $expected = "this has a white space";
    $actual = $this->rivescript->reply('special char test 1');

    $this->assertEquals($expected, $actual);
});

it("replaces the \\n tag with a new line", function () {
    $expected = "this has a\nnewline";
    $actual = $this->rivescript->reply('special char test 2');

    $this->assertEquals($expected, $actual);
});

it("replaces the \\/ tag with a forward slash", function () {
    $expected = "this adds a forward slash look /";
    $actual = $this->rivescript->reply('special char test 3');

    $this->assertEquals($expected, $actual);
});

it("replaces the \\# tag with a hashtag symbol", function () {
    $expected = "this adds a hashtag look #";
    $actual = $this->rivescript->reply('special char test 4');

    $this->assertEquals($expected, $actual);
});