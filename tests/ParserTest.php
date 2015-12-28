<?php

class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveWhitespace()
    {
        $parser = new \Vulcan\Rivescript\Parser;
        $line   = ' + hello   ';

        $this->assertEquals('+ hello', $parser->removeWhitespace($line));
    }
}
