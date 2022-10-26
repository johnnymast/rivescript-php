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


test("The topic label creates a new code object.", function () {
    $script = <<<EOF
> object info php
    phpinfo();
 < object
EOF;

    $this->rivescript->stream($script);

    $codeBlock = synapse()->brain->codeObject("info");

    $actual = ($codeBlock !== null);
    assertTrue($actual);

    $expected = "info";
    $actual = $codeBlock->getName();
    assertEquals($expected, $actual);

    $expected = "php";
    $actual = $codeBlock->getLanguage();
    assertEquals($expected, $actual);

    $expected = "phpinfo();";
    $actual = $codeBlock->getCode();
    assertEquals($expected, $actual);
});