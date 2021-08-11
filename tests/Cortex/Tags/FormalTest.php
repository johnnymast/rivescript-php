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


it("will transform text between {formal} and {/formal}", function () {
    $expected = "roger this is A Sentence With All First Chars Capitalized. curly bracket";
    $actual = $this->rivescript->reply("formal test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between <formal> and </formal>", function () {
    $expected = "roger this is A Sentence With All First Chars Capitalized. angled bracket";
    $actual = $this->rivescript->reply("formal test 2");

    $this->assertEquals($expected, $actual);
});