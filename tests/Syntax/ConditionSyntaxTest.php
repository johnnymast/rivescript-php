<?php

/**
 * Test the syntax for condition definitions.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Syntax
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Syntax;

use Axiom\Rivescript\Cortex\Node;

uses()
    ->group('syntax');

it('passes valid conditions', function () {
    $valid = [
        '==',
        'eq',
        '!=',
        'ne',
        '<>',
        '<',
        '<=',
        '>',
        '>='
    ];

    foreach ($valid as $condition) {
        $node = new Node("* <get name> {$condition} <star> => Response line", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }
});

it('rejects invalid response text', function () {
    $node = new Node("* <get name> eq <star> =>", 0);

    $expected = "Invalid format for !Condition: should be like `* value symbol value => response`";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid condition', function () {
    $node = new Node("* <get name> unknown <star> => RESPONSE LINE", 0);

    $expected = "Invalid format for !Condition: should be like `* value symbol value => response`";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});
