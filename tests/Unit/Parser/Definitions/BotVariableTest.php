<?php

/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript\Tests\Unit\Parser\Variables;

use Axiom\Rivescript\Parser\Parser;
use Axiom\Rivescript\Rivescript;

beforeEach(function () {
    $this->parser = new Parser(new Rivescript());
});

test(
    'Variables are parsed correctly.',
    function () {
        $code = "! var name = Purple";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["var"];

        expect($variables)->toHaveKey("name")
            ->and($variables["name"])->toBe("Purple");
    }
);

test(
    'Spaces are not required between the name, the value and the equal sign.',
    function () {
        $code = "! var color=red";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["var"];

        expect($variables)->toHaveKey("color")
            ->and($variables["color"])->toBe("red");
    }
);

test(
    'HTML can be in variable values.',
    function () {
        $code = "! var color = <b>yellow</b>";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["var"];

        expect($variables)->toHaveKey("color")
            ->and($variables["color"])->toBe("<b>yellow</b>");
    }
);

test(
    'An error should be send out if there is no space between the exclamation mar and the name of the variable.',
    function () {
        $this->markTestSkipped('Not implemented yet.');
    }
);

test(
    'Variables names and values are trimmed correctly.',
    function () {
        $this->markTestSkipped('Not implemented yet.');
    }
);

test(
    'Syntax errors should send out an error and stop parsing.',
    function () {
        $this->markTestSkipped('Not implemented yet.');
    }
);
