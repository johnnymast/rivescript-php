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


it("will transform text between {uppercase} and {/uppercase}", function () {
    $expected = "roger this is UPPERCASE";
    $actual = $this->rivescript->reply("uppercase test 1");

    $this->assertEquals($expected, $actual);
});

it("will transform text between <uppercase> and </uppercase>", function () {
    // TODO
});