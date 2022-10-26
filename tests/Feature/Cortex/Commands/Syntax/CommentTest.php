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


it('Should pass valid comments', function () {
    $line = "// This is a comment";

    $node = new Node($line, 0);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertTrue($valid);
});

it('Should detect deprecated hashtag comments', function () {
    $line = "# CommentCommand is invalid and deprecated.";

    $node = new Node($line, 10);
    $command = $node->getCommand();

    $valid = $command->isSyntaxValid();
    assertFalse($valid);

    $errors = $command->getSyntaxErrors();
    $expected = "Using the # symbol for comments is deprecated. Found on line 10";
    $actual = current($errors);

    assertEquals($expected, $actual);
});