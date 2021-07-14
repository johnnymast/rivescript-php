<?php

namespace Tests;

class MultTagTest extends ResponseTest
{
    public function testMultTagSettingValue()
    {
        $response = $this->rivescript->reply('multiply points by 9');
        $this->assertEquals('I\'ve multiplied your points points by 9.', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 0 points.', $response);
    }

    public function testMultTagAfterSettingValue()
    {
        $response = $this->rivescript->reply('set points');
        $this->assertEquals('Done!', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 4 points.', $response);

        $response = $this->rivescript->reply('multiply points by 9');
        $this->assertEquals('I\'ve multiplied your points points by 9.', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 36 points.', $response);
    }
//
    public function testMultTagWithoutVariableBeingDefined()
    {
        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have undefined points.', $response);

        $response = $this->rivescript->reply('multiply points by 9');
        $this->assertEquals('I\'ve multiplied your points points by 9.', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 0 points.', $response);
    }

    public function testMultTagWithStarTag() {
        $response = $this->rivescript->reply('set points');
        $this->assertEquals('Done!', $response);

        $response = $this->rivescript->reply('mult by 5');
        $this->assertEquals('Your points have been multiplied by 5', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 20 points.', $response);
    }
}
