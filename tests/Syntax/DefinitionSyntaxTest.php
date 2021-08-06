<?php

/**
 * Test the syntax for condition definitions.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Syntax
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Tests\Syntax;

use Axiom\Rivescript\Cortex\Node;

uses()
    ->group('syntax');

it('passes valid definition', function () {
    $node = new Node("! version = 2.0", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid definition', function () {
    $node = new Node("! version", 0);

    $expected = "Invalid format for !Definition line: must be '! type name = value' OR '! type = value'";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});