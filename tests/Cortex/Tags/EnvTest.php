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

it('can read global variables.', function() {
    $expected = "The topic is sensation.";
    $actual = $this->rivescript->reply("what is your global topic");
    $this->assertEquals($expected, $actual);
});
