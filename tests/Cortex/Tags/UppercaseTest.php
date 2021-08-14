<?php

/**
 * Test the {uppercase} and <uppercase> tag from the Uppercase class.
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


it("will transform text between {uppercase} and {/uppercase} (Single-Word)", function () {
    $expected = "roger this is UPPERCASE single-word with curly brackets.";
    $actual = $this->rivescript->reply("uppercase test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between {uppercase} and {/uppercase} (Multi-Word)", function () {
    $expected = "roger this is UPPERCASE MULTI-WORD with curly brackets.";
    $actual = $this->rivescript->reply("uppercase test 2");

    $this->assertEquals($expected, $actual);
});

it("will transform <uppercase> as alias of {uppercase}<star>{/uppercase} (Single-Word)", function () {
    $expected = "roger this is UPPERCASE with single-word with angled brackets.";
    $actual = $this->rivescript->reply("uppercase test 3 uppercase");

    $this->assertEquals($expected, $actual);
});

it("will transform <uppercase> as alias of {uppercase}<star>{/uppercase} (Multi-Word)", function () {
    $expected = "roger this is UPPERCASE MULTIWORD with angled brackets.";
    $actual = $this->rivescript->reply("uppercase test 4 uppercase multiword");

    $this->assertEquals($expected, $actual);
});