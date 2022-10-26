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

it('passes valid definition', function () {
    $node = new Node("! version = 2.0", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertTrue($valid);
});

it('rejects invalid definition', function () {
    $node = new Node("! version", 0);
    $command = $node->getCommand();

    $expected = "Invalid format for !DefinitionCommand line: must be '! type name = value' OR '! type = value'";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});