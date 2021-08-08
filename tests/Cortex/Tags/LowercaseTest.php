<?php

/**
 * Test the {uppercase} and <uppercase> tag from the Uppercase class.
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
    ->group('tags');


it("will transform text between {lowercase} and {/lowercase}", function () {
    $expected = "roger this is lowercase";
    $actual = $this->rivescript->reply("lowercase test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between <lowercase> and </lowercase>", function () {
    // TODO
});