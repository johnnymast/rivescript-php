<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Cortex\Node;

uses()
    ->group('feature_tests');

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
        $node = new Node("* <get name> {$condition} <star> => ResponseCommand line", 0);
        $command = $node->getCommand();


        $valid = $command->isSyntaxValid();
        assertTrue($valid);
    }
});

it('rejects invalid response text', function () {
    $node = new Node("* <get name> eq <star> =>", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Invalid format for !ConditionCommand: should be like `* value symbol value => response`";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});

it('rejects invalid condition', function () {
    $node = new Node("* <get name> unknown <star> => RESPONSE LINE", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Invalid format for !ConditionCommand: should be like `* value symbol value => response`";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});
