<?php

/**
 * Test the <bot> tag from the Bot class.
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

it("can read bot variables.", function () {
    $expected = "You can call me Beta.";
    $actual =  $this->rivescript->reply("what is your name");
    $this->assertEquals($expected, $actual);
});

it("can be used without text behind it", function() {
    $expected = "What can I do for you?";
    $actual = $this->rivescript->reply('Beta');

    $this->assertEquals($expected, $actual);
});
