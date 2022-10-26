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


it('passes valid begin', function () {
    $node = new Node("> begin", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertTrue($valid);
});

it('passes valid topic', function () {
    $node = new Node("> topic test", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertTrue($valid);
});
//
it('passes valid object', function () {
    $node = new Node("> object encode perl2", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertTrue($valid);
});

it('rejects invalid begin with extra parameters', function () {
    $node = new Node("> begin bleep", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $errors = $command->getSyntaxErrors();
    $actual = current($errors);
    $expected = "The 'begin' label takes no additional arguments, should be verbatim '> begin'";

    assertEquals($expected, $actual);
});


it('rejects invalid topics with uppercase parameters', function () {
    $node = new Node("> topic UpercaseName", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Topics should be lowercase and contain only numbers and letters!";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    $this->assertEquals($expected, $actual);
});

it('rejects invalid object', function () {
    $node = new Node("> object test Code", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Objects can only contain numbers and lowercase letters!";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    $this->assertEquals($expected, $actual);
});
