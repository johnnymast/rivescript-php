<?php

/**
 * Test the <input> tag from the Input tag class.
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

it('translates <input> to the client\'s last input', function () {
    $this->rivescript->reply("<input> test");

    $expected = "Your last input is: <input> test";
    $actual = $this->rivescript->reply("what is my last input");

    $this->assertEquals($expected, $actual);
});

it('translates unknown inputs to undefined', function () {
    $expected = "Your last input is: undefined";
    $actual = $this->rivescript->reply("try a undefined input");
    $this->assertEquals($expected, $actual);
});

it('translates <input1> to <input9> to the client\'s last 9 inputs', function () {
    for ($i = 1; $i < 10; $i++) {
        $this->rivescript->reply("input{$i}");
    }
    $expected = [
        "input1",
        "input2",
        "input3",
        "input4",
        "input5",
        "input6",
        "input7",
        "input8",
        "input9",
    ];

    $actual = array_values(synapse()->memory->inputs()->all());

    $this->assertEquals($expected, $actual);

    $expected = "input3";
    $actual = $this->rivescript->reply("what is input3");
    $this->assertEquals($expected, $actual);
});