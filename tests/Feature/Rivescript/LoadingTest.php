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
    ->beforeEach(hook: function () {
        $this->rivescript = new Rivescript();
    })
    ->group('feature_tests');


it("Should be tested")->skip("Not tested yet");
