<?php

namespace Tests;

use Axiom\Rivescript\Cortex\Node;

class DefinitionSyntaxTest extends ResponseTest
{
    public function testMissingValue()
    {
        $node = new Node("! version", 0);

        $expected = "Invalid format for !Definition line: must be '! type name = value' OR '! type = value'";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidDefinitionWithoutName()
    {
        $node = new Node("! version = 2.0", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

}
