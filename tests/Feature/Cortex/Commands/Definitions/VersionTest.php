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

it('Should store version in local memory', function () {
    $script =<<<EOF
! version = 2.0 
EOF;

    $this->rivescript->stream($script);

    $actual = synapse()->memory->local()->get('version');
    $expected = "2.0";

    assertEquals($expected, $actual);
});