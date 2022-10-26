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


it('Stores arrays with one item', function () {
    $script = <<<EOF
! array names = scott
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->arrays()->get('names');
    $expected = [
        'scott'
    ];

    assertEquals($expected, $actual);
});

it('Stores arrays with multiple items', function () {
    $script = <<<EOF
! array whatis = what is|what are|what was|what were
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->arrays()->get('whatis');
    $expected = [
        "what is",
        "what are",
        "what was",
        "what were"
    ];

    assertEquals($expected, $actual);
});

it('Local definition allows html', function () {
    $script = <<<EOF
! array html = <b>bold</b>
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->arrays()->get("html");
    $expected = [
        "<b>bold</b>"
    ];

    assertEquals($expected, $actual);
});

test('The keyword array can be used in the name of the array', function () {
    $script = <<<EOF
! array array = correct
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->arrays()->get("array");
    $expected = [
        "correct"
    ];

    assertEquals($expected, $actual);
});

test('The keyword array can be used in the value of the array', function () {
    $script = <<<EOF
! array myarray = array
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->arrays()->get("myarray");
    $expected = [
        "array"
    ];

    assertEquals($expected, $actual);
});
