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


it("will translate {person} to a person variable (Single-Word)", function () {
    $expected = 'umm... "my" curly bracket';
    $actual = $this->rivescript->reply("abc test 1 say your");

    $this->assertEquals($expected, $actual);
});

it("will translate {person} to a person variable (Multi-Word)", function () {
    $expected = 'umm... "I am awesome man" curly bracket';
    $actual = $this->rivescript->reply("abc test 2 say you are awesome man");

    $this->assertEquals($expected, $actual);
});

it("will translate <person> to a person variable (Single-Word)", function () {
    $expected = 'umm... "my" angled bracket';
    $actual = $this->rivescript->reply("abc test 3 say your");

    $this->assertEquals($expected, $actual);
});

it("will translate <person> to a person variable (Multi-Word)", function () {
    $expected = 'umm... "I am awesome man" angled bracket';
    $actual = $this->rivescript->reply("abc test 4 say you are awesome man");

    $this->assertEquals($expected, $actual);
});

it("will translate <person> unknown person variable to undefined (Multi-Word)", function () {
    $expected = 'umm... "undefined" angled bracket';
    $actual = $this->rivescript->reply("abc test 4 say socks");

    $this->assertEquals($expected, $actual);
});