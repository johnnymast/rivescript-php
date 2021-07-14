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
        $node->setAllowUtf8(true);

        $expected = null;
        $actual = $node->checkSyntax();

        $this->assertEquals($expected, $actual);
    }
}
