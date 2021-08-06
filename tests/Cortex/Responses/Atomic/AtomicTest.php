<?php

/**
 * Test the NotEquals class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Responses
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Response\Weighted;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../../resources/test.rive');
    })
    ->group('responses');

it('prioritizes the correct weight', function() {
    $expected = "Hello human.";
    $actual = $this->rivescript->reply('hello bot');

    $this->assertEquals($expected, $actual);
});