<?php

/**
 * Test the syntax for labels.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Syntax
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Tests;

use Axiom\Rivescript\Cortex\Node;

uses()
    ->group('syntax');

it('passes valid begin', function () {
    $node = new Node("> begin", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid topic', function () {
    $node = new Node("> topic test", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid object', function () {
    $node = new Node("> object encode perl2", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid begin with extra parameters', function () {
    $node = new Node("> begin bleep", 0);

    $expected = "The 'begin' label takes no additional arguments, should be verbatim '> begin'";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid topics with uppercase parameters', function () {
    $node = new Node("> topic UpercaseName", 0);

    $expected = "Topics should be lowercased and contain only numbers and letters!";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid object', function () {
    $node = new Node("> object test Code", 0);

    $expected = "Objects can only contain numbers and lowercase letters!";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});
