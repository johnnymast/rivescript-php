<?php

/**
 * Test the {random} tag from the Random class.
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

it("can pick a single random word at random", function () {
    $expected = ['word1', 'word2', 'word3'];
    $actual = $this->rivescript->reply('random word test');

    $this->assertContains($actual, $expected);
});

it("can pick a set of words at random", function () {
    $expected = ['Result set 1', 'Result set 2', 'Result set 3'];
    $actual = $this->rivescript->reply('random word set test');

    $this->assertContains($actual, $expected);
});