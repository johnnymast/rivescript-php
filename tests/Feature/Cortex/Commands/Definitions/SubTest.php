<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(function () {
        $this->rivescript = new Rivescript();;
    })
    ->group('feature_tests');



test('Substitutions can be assigned', function () {
    $script =<<<EOF
! sub what's  = what is
! sub what're = what are
! sub what'd  = what did
! sub a/s/l   = age sex location
! sub brb     = be right back
! sub afk     = away from keyboard
! sub l o l   = lol
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->substitute()->get('brb');
    $expected = "be right back";

    assertEquals($expected, $actual);

    $actual = synapse()->memory->substitute()->get('what\'re');
    $expected = "what are";

    assertEquals($expected, $actual);
});

test('Html can be used in Substitutions', function() {
    $script =<<<EOF
! sub afk     = <strong>AFK</strong>
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->substitute()->get('afk');
    $expected = "<strong>AFK</strong>";

    assertEquals($expected, $actual);
});

test('The keyword sub can be used in the name of the substitute', function () {
    $script = <<<EOF
! sub sub = right
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->substitute()->get("sub");
    $expected = "right";


    assertEquals($expected, $actual);
});

test('The keyword person can be used in the value of the person variable', function () {
    $script = <<<EOF
! sub mysub = sub
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->substitute()->get("mysub");
    $expected = "sub";

    assertEquals($expected, $actual);
});