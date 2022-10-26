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



it('Should only assign allowed values', function () {
    $script =<<<EOF
! local concat = space
! local bleep = blop
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->local()->get('concat');
    $expected = "space";

    assertEquals($expected, $actual);

    $actual = synapse()->memory->local()->get('bleep');
    $expected = null;

    assertEquals($expected, $actual);
});