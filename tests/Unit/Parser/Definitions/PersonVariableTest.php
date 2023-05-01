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
        $code = "! person i am = you are";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["person"];

        expect($variables)->toHaveKey("i am")
            ->and($variables["i am"])->toBe("you are");
    }
);

test(
    'Spaces are not required between the name, the value and the equal sign.',
    function () {
        $code = "! person you are=I am";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["person"];

        expect($variables)->toHaveKey("you are")
            ->and($variables["you are"])->toBe("I am");
    }
);

test(
    'HTML can be in variable values.',
    function () {
        $code = "! person i = <b>you</b>";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["person"];

        expect($variables)->toHaveKey("i")
            ->and($variables["i"])->toBe("<b>you</b>");
    }
);

test(
    'A person variable named person should be allowed.',
    function() {
        $this->markTestSkipped('Not implemented yet.');
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

