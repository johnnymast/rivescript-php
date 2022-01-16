<?php

namespace Tests\Cortex\Tags;

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__ . '/../../resources/tags/tags.rive');
    })
    ->group('tags');
//
//
//it ('Escapes unknown tags.', function() {
//    $expected = "i <do>";
//    $actual = $this->rivescript->reply("escape my <html>");
//
//    $this->assertEquals($expected, $actual);
//});