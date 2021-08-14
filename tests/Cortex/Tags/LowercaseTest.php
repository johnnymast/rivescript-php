<?php

/**
 * Test the {lowercase} and <lowercase> tag from the Lowercase class.
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


it("will transform text between {lowercase} and {/lowercase} (Single-Word)", function () {
    $expected = "roger this is lowercase single-word with curly brackets.";
    $actual = $this->rivescript->reply("lowercase test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between {lowercase} and {/lowercase} (Multi-Word)", function () {
    $expected = "roger this is lowercase multiword with curly brackets.";
    $actual = $this->rivescript->reply("lowercase test 2");

    $this->assertEquals($expected, $actual);
});

it("will transform <lowercase> as alias of {lowercase}<star>{/lowercase} (Single-Word)", function () {
    $expected = "roger this is bleep single-word with angled brackets.";
    $actual = $this->rivescript->reply("lowercase test 3 BLEEP");

    $this->assertEquals($expected, $actual);
});

it("will transform <lowercase> as alias of {lowercase}<star>{/lowercase} (Multi-Word)", function () {
    $expected = "roger this is test case with angled brackets.";
    $actual = $this->rivescript->reply("lowercase test 4 TEST CASE");

    $this->assertEquals($expected, $actual);
});