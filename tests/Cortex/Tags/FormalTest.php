<?php

/**
 * Test the {formal} and <formal> tag from the Formal class.
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


it("will transform text between {formal}and{/formal} (Single-Word)", function () {
    $expected = "roger this is First. curly bracket";
    $actual = $this->rivescript->reply("formal test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between {formal}and{/formal} (Multi-Word)", function () {
    $expected = "roger this is First And Second. curly bracket";
    $actual = $this->rivescript->reply("formal test 2");

    $this->assertEquals($expected, $actual);
});


it("will transform <formal> as alias of {formal}<star>{/formal} (Single-Word)", function () {
    $expected = "roger this is Single. angled bracket";
    $actual = $this->rivescript->reply("formal test 3 single");

    $this->assertEquals($expected, $actual);
});

it("will transform <formal> as alias of {formal}<star>{/formal} (Multi-Word)", function () {
    $expected = "roger this is Multi Word. angled bracket";
    $actual = $this->rivescript->reply("formal test 4 multi word");

    $this->assertEquals($expected, $actual);
});

it("RSTS formal test", function () {
    $this->rivescript->stream("// RiveScript source is included as a YAML\n// literal-style multi-line string.\n+ hello bot\n- Hello human!\n\n+ how are you\n- Good, you?\n- Alright, how are you?\n\n+ my name is *\n- <set name=<formal>>Nice to meet you, <get name>.\n\n+ who am i\n- Your name is <get name>, right?\n");
    $expected = "Nice to meet you, Alice.";
    $actual = $this->rivescript->reply("my name formal is Alice");

    $this->assertEquals($expected, $actual);
});
