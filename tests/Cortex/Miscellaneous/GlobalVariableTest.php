<?php


use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();
        $this->rivescript->load(__DIR__.'/../../resources/tags/tags.rive');
    })
    ->group('miscellaneous');


it("Should be able to set and get global variables", function () {

    $script =<<< EOF
! global debug = false

+ debug mode
- Debug mode is: <env debug>

+ set debug mode *
- <env debug=<star>>Switched to <star>. 
EOF;

   $this->rivescript->stream($script);
   $this->rivescript->reply("Set debug mode true");

   $expected = "Debug mode is: true";
   $actual = $this->rivescript->reply("Debug mode?");

   $this->assertEquals($expected, $actual);
});
