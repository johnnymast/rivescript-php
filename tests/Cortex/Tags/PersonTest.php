<?php

/**
 * Test the {lowercase} and <lowercase> tag from the Lowercase class.
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


it("will translate {person} to a person variable (Single-Word)", function () {
    $expected = "umm... my awesome";
    $actual = $this->rivescript->reply("abc test 1 say your awesome");

    $this->assertEquals($expected, $actual);
});
