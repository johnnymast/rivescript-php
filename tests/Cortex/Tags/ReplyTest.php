<?php

/**
 * Test the <reply> tag from the Reply tag class.
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

it('translates <reply> to the client\'s last reply', function () {
    $this->rivescript->reply("reset my points");

    $expected = "Your points are 0";
    $actual = $this->rivescript->reply("what is your last reply");

    $this->assertEquals($expected, $actual);
});

it('translates unknown replies to undefined', function () {
    $expected = "undefined";
    $actual = $this->rivescript->reply('what is your last reply');
    $this->assertEquals($expected, $actual);
});

it('translates <reply1> to <reply9> to the client\'s last 9 replies', function () {
    // Set the points to 0
    synapse()->memory->user('local-user')->put("points", 0);

    // 9 Actual replies
    for ($i = 0; $i < 10; $i++) {
        $this->rivescript->reply("Add point 1");
    }

    $actual = synapse()->memory->replies()->all();
    $actual = array_values($actual);
    $expected = [
        "Your points are now 2",
        "Your points are now 3",
        "Your points are now 4",
        "Your points are now 5",
        "Your points are now 6",
        "Your points are now 7",
        "Your points are now 8",
        "Your points are now 9",
        "Your points are now 10",
    ];
    $this->assertEquals($expected, $actual);

    $expected = "Your points are now 3";
    $actual = $this->rivescript->reply("what is reply2");

    $this->assertEquals($expected, $actual);
});