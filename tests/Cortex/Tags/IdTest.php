<?php

/**
 * Test the <id> tag from the Id class.
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

it("can return the user id", function () {
    $expected = "yes the id is local-user";
    $actual = $this->rivescript->reply('do you have an id');

    $this->assertEquals($actual, $expected);
});