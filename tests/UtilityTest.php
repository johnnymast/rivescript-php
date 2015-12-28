<?php

class UtilityTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveWhitespace()
    {
        $utility = new \Vulcan\Rivescript\Utility;
        $line   = ' + hello   ';

        $this->assertEquals('+ hello', $utility->removeWhitespace($line));
    }

    public function testStartsWith()
    {
        $utility = new \Vulcan\Rivescript\Utility;
        $haystack = '/* This is a comment *//';
        $needle   = '/*';

        $this->assertEquals(true, $utility->startsWith($haystack, $needle));
    }

    public function testEndsWith()
    {
        $utility = new \Vulcan\Rivescript\Utility;
        $haystack = '/* This is a comment */';
        $needle   = '*/';

        $this->assertEquals(true, $utility->endsWith($haystack, $needle));
    }
}
