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

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('tags');

it('can subtract a value.', function() {
    $response = $this->rivescript->reply('remove 5 points');
    $this->assertEquals('I\'ve removed 5 points to your account.', $response);

    $response = $this->rivescript->reply('how many points do i have');
    $this->assertEquals('You have -5 points.', $response);
});

it('can subtract a value from an existing value.', function() {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('how many points do i have');
    $this->assertEquals('You have 4 points.', $response);

    $response = $this->rivescript->reply('remove 5 points');
    $this->assertEquals('I\'ve removed 5 points to your account.', $response);

    $response = $this->rivescript->reply('how many points do i have');
    $this->assertEquals('You have -1 points.', $response);
});

it('can subtract a value given by a *.', function() {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('remove 2 points from total');
    $this->assertEquals('I\'ve removed 2 points to your account.', $response);

    $response = $this->rivescript->reply('how many points do i have');
    $this->assertEquals('You have 2 points.', $response);
});
