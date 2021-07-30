<?php

namespace Tests;

use Axiom\Rivescript\Cortex\Node;

class TriggerSyntaxTest extends ResponseTest
{

    public function testTriggersCanOnlyContainValidCharacters()
    {
        $node = new Node("+ this is invalid?", 0);

        $expected = "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > =";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testTriggersCanOnlyContainValidCharactersForUTF8Mode()
    {
        $node = new Node("+ this is invalid?.", 0);
        $node->setAllowUtf8(true);

        $expected = "Triggers can't contain uppercase letters, backslashes or dots in UTF-8 mode.";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testTriggerValidValueNonUTF8Mode()
    {
        $node = new Node("+ this is valid", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testTriggerValidValueUTF8Mode()
    {
        $node = new Node("+ this is valid", 0);
      //  $node->setAllowUtf8(true);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testUnmatchedOpenOrCloseParenthesisBrackets() {
        $node = new Node("+ this is missing an opentag)", 0);

        $expected = "Unmatched right parenthesis bracket ()";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);

        $node = new Node("+ this is missing an (opentag", 0);

        $expected = "Unmatched left parenthesis bracket ()";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidOpenOrCloseParenthesisBrackets() {
        $node = new Node("+ this is missing an <opentag>", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testUnmatchedOpenOrCloseSquareBrackets() {
        $node = new Node("+ this is missing an opentag]", 0);

        $expected = "Unmatched right square bracket []";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);

        $node = new Node("+ this is missing an [opentag", 0);

        $expected = "Unmatched left square bracket []";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidOpenOrCloseSquareBrackets() {
        $node = new Node("+ this is missing an [opentag]", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testUnmatchedOpenOrCloseCurlyBrackets() {
        $node = new Node("+ this is missing an opentag}", 0);

        $expected = "Unmatched right curly bracket {}";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);

        $node = new Node("+ this is missing an {opentag", 0);

        $expected = "Unmatched left curly bracket {}";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidOpenOrCloseCurlyBrackets() {
        $node = new Node("+ this is missing an {opentag}", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testUnmatchedOpenOrCloseAngledBrackets() {
        $node = new Node("+ this is missing an opentag>", 0);

        $expected = "Unmatched right angled bracket <>";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);

        $node = new Node("+ this is missing an <opentag", 0);

        $expected = "Unmatched left angled bracket <>";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidOpenOrCloseAngledBrackets() {
        $node = new Node("+ this is missing an <opentag>", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }
}
