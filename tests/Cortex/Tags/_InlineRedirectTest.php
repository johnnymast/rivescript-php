<?php

/**
 * Test the {@topic name}/<@> tag from the InlineRedirect class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('Tags');

it("will perform an inline redirection for Tags starting with {@..}. ", function () {
    $expected = "hello That is result 1";
    $actual = $this->rivescript->reply("redirect test 1");

    $this->assertEquals($expected, $actual);
});

it("will redirect to a new trigger when using <@>. ", function () {
    $expected = "redirect 2 This is redirect 2";
    $actual = $this->rivescript->reply("redirect test 2 rr");

    $this->assertEquals($expected, $actual);
});