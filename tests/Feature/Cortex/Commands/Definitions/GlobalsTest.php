<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();;
    })
    ->group('feature_tests');

it('Should store global variables', function () {
    $script = <<<EOF
! global debug = 1
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->global()->get("debug");
    $expected = "1";

    assertEquals($expected, $actual);
});

it('Global definition allows html', function () {
    $script = <<<EOF
! global html = <b>bold</b>
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->global()->get("html");
    $expected = "<b>bold</b>";

    assertEquals($expected, $actual);
});

test('The keyword global can be used in the name of the global', function () {
    $script = <<<EOF
! global global = correct
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->global()->get("global");
    $expected = "correct";


    assertEquals($expected, $actual);
});

test('The keyword global can be used in the value of the global', function () {
    $script = <<<EOF
! global myglobal = global
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->global()->get("myglobal");
    $expected = "global";

    assertEquals($expected, $actual);
});


test('Default global variable depth exists and has value 25', function () {
    $actual = synapse()->memory->global()->get("depth");
    $expected = 25;

    $this->assertEquals($expected, $actual);
});

test('Default global variable debug exists and has value false', function () {
    $actual = synapse()->memory->global()->get("debug");
    $expected = false;

    $this->assertEquals($expected, $actual);
});
