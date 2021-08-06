<?php

/**
 * Test the Mult tag from the Mult class.
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

it('can multiply values.', function () {
    $response = $this->rivescript->reply('multiply points by 9');
    $this->assertEquals('I\'ve multiplied your points points by 9.', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 0 points.', $response);
});

it('can multiply existing values', function () {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 4 points.', $response);

    $response = $this->rivescript->reply('multiply points by 9');
    $this->assertEquals('I\'ve multiplied your points points by 9.', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 36 points.', $response);
});

it('can multiply * values.', function () {
    $response = $this->rivescript->reply('set points');
    $this->assertEquals('Done!', $response);

    $response = $this->rivescript->reply('mult by 5');
    $this->assertEquals('Your points have been multiplied by 5', $response);

    $response = $this->rivescript->reply('how many points do i have?');
    $this->assertEquals('You have 20 points.', $response);
});
