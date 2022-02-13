<?php

/**
 * Test the <get> tag from the Get class.
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
    ->group('Tags');;

it('can get user variables.', function () {
    $name = "gettest";
    $this->rivescript->reply("my name is {$name}");

    $expected = "Your name is {$name}!";
    $actual = $this->rivescript->reply("what is my name");
    $this->assertEquals($expected, $actual);
});
