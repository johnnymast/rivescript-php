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
    ->beforeEach(function () {
        $this->rivescript = new \Axiom\Rivescript\Rivescript();
    })
    ->group('feature_tests');


it('passes valid square brackets', function () {
    $node = new Node("+ this is missing an [opentag]", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertTrue($valid);
});

it('rejects invalid characters', function () {
    $node = new Node("+ this is invalid?", 0);
    $command = $node->getCommand();

    $actual = $command->isSyntaxValid();
    assertFalse($actual);

    $expected = "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > =";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});

it('rejects invalid characters for UTF8 mode', closure: function () {
    synapse()->rivescript->utf8 = true;

    $node = new Node("+ this is invalid?.", 0);
    $command = $node->getCommand();

    $actual = $command->isSyntaxValid();
    assertFalse($actual);

    $expected = "Triggers can't contain uppercase letters, backslashes or dots in UTF-8 mode.";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});

it('rejects unmatched parenthesis brackets', function () {
    $node = new Node("+ this is missing an opentag)", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched right parenthesis bracket ()";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);


    $node = new Node("+ this is missing an (opentag", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched left parenthesis bracket ()";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});

it('rejects unmatched square brackets', function () {
    $node = new Node("+ this is missing an opentag]", 0);
    $command = $node->getCommand();


    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched right square bracket []";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);

    $node = new Node("+ this is missing an [opentag", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);


    $expected = "Unmatched left square bracket []";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});

it('rejects unmatched curly brackets', function () {
    $node = new Node("+ this is missing an opentag}", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched right curly bracket {}";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);

    $node = new Node("+ this is missing an {opentag", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched left curly bracket {}";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});

it('rejects unmatched angled brackets', function () {
    $node = new Node("+ this is missing an opentag>", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched right angled bracket <>";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);

    $node = new Node("+ this is missing an <opentag", 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $expected = "Unmatched left angled bracket <>";
    $errors = $command->getSyntaxErrors();
    $actual = current($errors);

    assertEquals($expected, $actual);
});
