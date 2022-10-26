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



test('Variables can be assigned', function () {
    $script =<<<EOF
  ! var name      = RiveScript Bot
  ! var age       = 0
  ! var gender    = androgynous
  ! var location  = Cyberspace
  ! var generator = RiveScript
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->variables()->get('name');
    $expected = "RiveScript Bot";

    assertEquals($expected, $actual);

    $actual = synapse()->memory->variables()->get('location');
    $expected = "Cyberspace";

    assertEquals($expected, $actual);
});

test('Html can be used in Variables', function() {
    $script =<<<EOF
! var myvar     = <i>italic</i>
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->variables()->get('myvar');
    $expected = "<i>italic</i>";

    assertEquals($expected, $actual);
});


test('The keyword var can be used in the name of the variable', function () {
    $script = <<<EOF
! var var = yeps
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->variables()->get("var");
    $expected = "yeps";


    assertEquals($expected, $actual);
});

test('The keyword var can be used in the value of the variable', function () {
    $script = <<<EOF
! var myvar = var
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->variables()->get("myvar");
    $expected = "var";

    assertEquals($expected, $actual);
});