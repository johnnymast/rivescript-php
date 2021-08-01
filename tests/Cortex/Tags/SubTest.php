<?php

/**
 * Test the Sub tag from the Sub class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Tags;

use Tests\ResponseTest;

class SubTest extends ResponseTest
{
    public function testSubTagSettingValue()
    {
        $response = $this->rivescript->reply('remove 5 points');
        $this->assertEquals('I\'ve removed 5 points to your account.', $response);

        $response = $this->rivescript->reply('how many points do i have');
        $this->assertEquals('You have -5 points.', $response);
    }

    public function testSubTagAfterSettingValue()
    {
        $response = $this->rivescript->reply('set points');
        $this->assertEquals('Done!', $response);

        $response = $this->rivescript->reply('how many points do i have');
        $this->assertEquals('You have 4 points.', $response);

        $response = $this->rivescript->reply('remove 5 points');
        $this->assertEquals('I\'ve removed 5 points to your account.', $response);

        $response = $this->rivescript->reply('how many points do i have');
        $this->assertEquals('You have -1 points.', $response);
    }

    public function testSubTagWithoutVariableBeingDefined()
    {
        $response = $this->rivescript->reply('how many points do i have');
        $this->assertEquals('You have undefined points.', $response);

        $response = $this->rivescript->reply('remove 5 points');
        $this->assertEquals('I\'ve removed 5 points to your account.', $response);

        $response = $this->rivescript->reply('how many points do i have');
        $this->assertEquals('You have -5 points.', $response);
    }

    public function testSubTagWithStarTag() {
        $response = $this->rivescript->reply('set points');
        $this->assertEquals('Done!', $response);

        $response = $this->rivescript->reply('remove 2 points from total');
        $this->assertEquals('I\'ve removed 2 points to your account.', $response);

        $response = $this->rivescript->reply('how many points do i have');
        $this->assertEquals('You have 2 points.', $response);
    }
}
