<?php

namespace Tests;

class AddTagTest extends ResponseTest
{
    public function testAddTagSettingValue() {
        $response = $this->rivescript->reply('give me 5 points');
        $this->assertEquals('I\'ve added 5 points to your account.', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 5 points.', $response);
    }

    public function testAddTagAfterSettingValue() {
        $response = $this->rivescript->reply('set points');
        $this->assertEquals('Done!', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 4 points.', $response);

        $response = $this->rivescript->reply('give me 5 points');
        $this->assertEquals('I\'ve added 5 points to your account.', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 9 points.', $response);
    }

    public function testAddTagWithoutVariableBeingDefined()
    {
        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have undefined points.', $response);

        $response = $this->rivescript->reply('give me 5 points');
        $this->assertEquals('I\'ve added 5 points to your account.', $response);
    }

    public function testAddTagWithStarTag() {
        $response = $this->rivescript->reply('set points');
        $this->assertEquals('Done!', $response);

        $response = $this->rivescript->reply('add 91 points');
        $this->assertEquals('it has been done. 91 points have been added.', $response);

        $response = $this->rivescript->reply('how many points do i have?');
        $this->assertEquals('You have 95 points.', $response);
    }
}
