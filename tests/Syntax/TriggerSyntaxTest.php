<?php

namespace Tests\Syntax;

use Axiom\Rivescript\Cortex\Node;

uses()
    ->group('syntax');

it('passes valid characters', function () {
    $node = new Node("+ this is valid", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid characters for UTF8 mode.', function () {
    $node = new Node("+ this is valid", 0);
    $node->setAllowUtf8(true);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid angled brackets', function () {
    $node = new Node("+ this is missing an <opentag>", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid curly brackets', function () {
    $node = new Node("+ this is missing an {opentag}", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid parenthesis brackets', function () {
    $node = new Node("+ this is missing an <opentag>", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('passes valid square brackets', function () {
    $node = new Node("+ this is missing an [opentag]", 0);

    $expected = null;
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid characters', function () {
    $node = new Node("+ this is invalid?", 0);

    $expected = "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > =";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects invalid characters for UTF8 mode', function () {
    $node = new Node("+ this is invalid?.", 0);
    $node->setAllowUtf8(true);

    $expected = "Triggers can't contain uppercase letters, backslashes or dots in UTF-8 mode.";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects unmatched parenthesis brackets', function () {
    $node = new Node("+ this is missing an opentag)", 0);

    $expected = "Unmatched right parenthesis bracket ()";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);

    $node = new Node("+ this is missing an (opentag", 0);

    $expected = "Unmatched left parenthesis bracket ()";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects unmatched square brackets', function () {
    $node = new Node("+ this is missing an opentag]", 0);

    $expected = "Unmatched right square bracket []";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);

    $node = new Node("+ this is missing an [opentag", 0);

    $expected = "Unmatched left square bracket []";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects unmatched curly brackets', function () {
    $node = new Node("+ this is missing an opentag}", 0);

    $expected = "Unmatched right curly bracket {}";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);

    $node = new Node("+ this is missing an {opentag", 0);

    $expected = "Unmatched left curly bracket {}";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});

it('rejects unmatched angled brackets', function () {
    $node = new Node("+ this is missing an opentag>", 0);

    $expected = "Unmatched right angled bracket <>";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);

    $node = new Node("+ this is missing an <opentag", 0);

    $expected = "Unmatched left angled bracket <>";
    $actual = $node->checkSyntax();

    $this->assertEquals($expected, $actual);
});
