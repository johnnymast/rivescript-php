<?php

/**
 * Test the {person} and <person> tag from the Person class.
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


it("will translate {person}person var{/person} to a person variable (Single-Word)", function () {
    $expected = 'umm... "your" curly bracket';
    $actual = $this->rivescript->reply("person test 1");

    $this->assertEquals($expected, $actual);
});

it("will translate {person}person var{/person} to a person variable (Multi-Word)", function () {
    $expected = 'umm... "I am" curly bracket';
    $actual = $this->rivescript->reply("person test 2");

    $this->assertEquals($expected, $actual);
});

it("will translate <person> as alias of {person}<star>{/person} (Single-Word)", function () {
    $expected = 'umm... "my" angled bracket';
    $actual = $this->rivescript->reply("person test 3 your");

    $this->assertEquals($expected, $actual);
});

it("will translate <person> as alias of {person}<star>{/person} (Multi-Word)", function () {
    $expected = 'umm... "I am" angled bracket';
    $actual = $this->rivescript->reply("person test 4 you are");

    $this->assertEquals($expected, $actual);
});