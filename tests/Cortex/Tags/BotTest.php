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

uses()
    ->group('tags');

it('can read bot variables.', function() {
    $expected = "You can call me Beta.";
    $actual =  $this->rivescript->reply('what is your name');
    $this->assertEquals($expected, $actual);
});
