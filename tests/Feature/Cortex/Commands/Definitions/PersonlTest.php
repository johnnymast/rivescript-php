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


it('Assigns person variables', function () {
    $script =<<<EOF
  ! person you are = I am
  ! person i am    = you are
  ! person you     = I
  ! person i       = you
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->person()->get('you are');
    $expected = "I am";

    assertEquals($expected, $actual);

    $actual = synapse()->memory->local()->get('bleep');
    $expected = null;

    assertEquals($expected, $actual);
});

it('Person definition allows html', function () {
    $script = <<<EOF
! person html = <b>bold</b>
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->person()->get("html");
    $expected = "<b>bold</b>";

    assertEquals($expected, $actual);
});

test('The keyword global can be used in the name of the person variable', function () {
    $script = <<<EOF
! person person = yep
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->person()->get("person");
    $expected = "yep";


    assertEquals($expected, $actual);
});

test('The keyword person can be used in the value of the person variable', function () {
    $script = <<<EOF
! person myperson = person
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->person()->get("myperson");
    $expected = "person";

    assertEquals($expected, $actual);
});