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
        $this->rivescript = new Rivescript();
    })
    ->group('feature_tests');

test("The begin label should create the __begin__ topic", function () {
    $script = <<<EOF
  > begin
    + request
    - {ok}
  < begin
EOF;

    $this->rivescript->stream($script);

    $actual = (synapse()->brain->topic("__begin__") !== null);
    assertTrue($actual);

});