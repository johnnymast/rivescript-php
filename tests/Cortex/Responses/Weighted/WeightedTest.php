<?php

/**
 * Test the Weighted class.
 *
 * @package      Rivescript-php
 * @subpackage   Tests
 * @category     Responses
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Tests\Cortex\Response;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../../resources/test.rive');
    })
    ->group('responses');

it('Prioritizes the correct weight', function () {
    $expected = "i am 2 kilo";
    $actual = $this->rivescript->reply('weight test');

    $this->assertEquals($expected, $actual);
});