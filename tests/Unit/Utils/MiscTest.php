<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Utils\Misc;

uses()
    ->beforeEach(function () {
    })
    ->group('unit_tests');

test('formatString() works without parameters', function () {
    $expected = "RESULT";
    $actual = Misc::formatString("RESULT");
    
    $this->assertEquals($expected, $actual);
});

test('formatString() works with one parameter', function () {
    $expected = "This Works";
    $actual = Misc::formatString("This :arg", ['arg' => 'Works']);

    $this->assertEquals($expected, $actual);
});

test('formatString() works with multiple parameter', function () {
    $expected = "Result: one two three";
    $actual = Misc::formatString(
        "Result: :arg1 :arg2 :arg3",
        [
            'arg1' => 'one',
            'arg2' => 'two',
            'arg3' => 'three'
        ]
    );

    $this->assertEquals($expected, $actual);
});
