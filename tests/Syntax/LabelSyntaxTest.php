<?php

namespace Tests;

use Axiom\Rivescript\Cortex\Node;

class LabelSyntaxTest extends ResponseTest
{
    public function testMissingValue()
    {
        $node = new Node("! version", 0);

        $expected = "Invalid format for !Definition line: must be '! type name = value' OR '! type = value'";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidBeginWithExtraParameters()
    {
        $node = new Node("> begin bleep", 0);

        $expected = "The 'begin' label takes no additional arguments, should be verbatim '> begin'";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidTopicWithExtraUppercaseParameters()
    {
        $node = new Node("> topic UpercaseName", 0);

        $expected = "Topics should be lowercased and contain only numbers and letters!";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidObjectDefinition()
    {
        $node = new Node("> object test Code", 0);

        $expected = "Objects can only contain numbers and lowercase letters!";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidBegin()
    {
        $node = new Node("> begin", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidTopic()
    {
        $node = new Node("> topic test", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidObject()
    {
        $node = new Node("> object encode perl2", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }
}
