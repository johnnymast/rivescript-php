<?php

namespace Tests;

use Axiom\Rivescript\Cortex\Node;

class ConditionSyntaxTest extends ResponseTest
{

    public function testValidConditionLine()
    {
        $node = new Node("* <get name> eq <star>    => I know, you told me that already.", 0);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testValidConditionLineWithValidConditions()
    {
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
    }

    public function testInvalidConditionWithoutResponseText()
    {
        $node = new Node("* <get name> eq <star> =>", 0);

        $expected = "Invalid format for !Condition: should be like `* value symbol value => response`";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }

    public function testInvalidConditionWithoutValidCondition()
    {
        $node = new Node("* <get name> unknown <star> => RESPONSE LINE", 0);

        $expected = "Invalid format for !Condition: should be like `* value symbol value => response`";
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }
}
