<?php

namespace Tests;

class VariableTest extends ResponseTest
{
    public function testSubstitute()
    {
        $response = $this->rivescript->reply('Beta');

        $this->assertEquals('What can I do for you?', $response);
    }
}
