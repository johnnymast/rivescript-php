<?php

/**
 * Test the <add> tag from the Add class.
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
    ->group('Tags');


it('can add values.', function () {
    $response = $this->rivescript->reply('give me 5 points');
    $this->assertEquals('I\'ve added 5 points to your account.', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 5 points.', $response);
});

it('can add values on top of existing values.', function () {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 4 points.', $response);

    $response = $this->rivescript->reply('give me 5 points');
    $this->assertEquals('I\'ve added 5 points to your account.', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 9 points.', $response);
});

it('can add value if variable is still undefined.', function () {
    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have undefined points.', $response);

    $response = $this->rivescript->reply('give me 5 points');
    $this->assertEquals('I\'ve added 5 points to your account.', $response);
});

it('can add values from a * value.', function () {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('add 91 points');
    $this->assertEquals('it has been done. 91 points have been added.', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 95 points.', $response);
});
