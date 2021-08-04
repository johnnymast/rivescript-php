<?php

/**
 * Test the Div tag from the Div class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Tags;

uses()
    ->group('tags');

it('can divide a value.', function () {
    $response = $this->rivescript->reply('divide points by 7');
    $this->assertEquals('I\'ve divided your points points by 7.', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 0 points.', $response);
});

it('can divide an existing value.', function () {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 4 points.', $response);

    $response = $this->rivescript->reply('add 5 points');
    $this->assertEquals('it has been done. 5 points have been added.', $response);

    $response = $this->rivescript->reply('divide by 3');
    $this->assertEquals('Your points have been divided by 3', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 3 points.', $response);
});

it('can divide a value passed by *.', function () {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('add 4 points');
    $this->assertEquals('it has been done. 4 points have been added.', $response);

    $response = $this->rivescript->reply('divide by 2');
    $this->assertEquals('Your points have been divided by 2', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 4 points.', $response);
});

