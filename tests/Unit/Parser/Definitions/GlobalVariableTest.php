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
        $code = "! global debug = Purple";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["global"];

        expect($variables)->toHaveKey("debug")
            ->and($variables["debug"])->toBe("Purple");
    }
);

test(
    'Spaces are not required between the name, the value and the equal sign.',
    function () {
        $code = "! global debug=false";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["global"];

        expect($variables)->toHaveKey("debug")
            ->and($variables["debug"])->toBe("false");
    }
);

test(
    'HTML can be in variable values.',
    function () {
        $code = "! global htm = <b>html</b>";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["global"];

        expect($variables)->toHaveKey("htm")
            ->and($variables["htm"])->toBe("<b>html</b>");
    }
);

test(
    'A global variable named global should be allowed.',
    function() {
        $code = "! global global =value";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["global"];

        expect($variables)->toHaveKey("global")
            ->and($variables["global"])->toBe("value");
    }
);

test(
    'Default global variables can be overwritten.',
    function () {
        $this->markTestSkipped('Not implemented yet.');
    }
);

test(
    'An error should be send out if there is no space between the exclamation mark and the name of the variable.',
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

