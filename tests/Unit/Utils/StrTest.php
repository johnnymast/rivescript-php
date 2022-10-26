<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Utils\Str;

uses()
    ->beforeEach(function () {
    })
    ->group('unit_tests');

test('removeWhitespace() removes whitespace', function () {

    $expected = "RESULT";
    $actual = Str::removeWhitespace("RESULT\r\n");
    $this->assertEquals($expected, $actual);
});

test('startsWith() should returns true if valid.', function () {
    $string = "HELLO WORLD";
    $result = Str::startsWith($string, "HELLO");
    $this->assertTrue($result);
});

test('startsWith() should returns false if invalid.', function () {
    $string = "HELLO WORLD";
    $result = Str::startsWith($string, "WORLD");
    $this->assertFalse($result);
});

test('endsWith() should returns true if valid.', function () {
    $string = "HELLO WORLD";
    $result = Str::endsWith($string, "WORLD");
    $this->assertTrue($result);
});

test('endsWith() should returns false if invalid.', function () {
    $string = "HELLO WORLD";
    $result = Str::endsWith($string, "HELLO");
    $this->assertFalse($result);
});