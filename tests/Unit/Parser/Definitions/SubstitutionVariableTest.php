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
        $code = "! sub a/s/l  = age sex location";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["sub"];

        expect($variables)->toHaveKey("a/s/l")
            ->and($variables["a/s/l"])->toBe("age sex location");
    }
);

test(
    'Spaces are not required between the name, the value and the equal sign.',
    function () {
        $code = "! sub afk=away from keyboard";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["sub"];

        expect($variables)->toHaveKey("afk")
            ->and($variables["afk"])->toBe("away from keyboard");
    }
);

test(
    'HTML can be in variable values.',
    function () {
        $code = "! sub title = <title>title here</title>";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["sub"];

        expect($variables)->toHaveKey("title")
            ->and($variables["title"])->toBe("<title>title here</title>");
    }
);

test(
    'A sub variable named sub should be allowed.',
    function() {
        $this->markTestSkipped('Not implemented yet.');
    }
);


test(
    'An error should be send out if there is no space between the exclamation mark and the name of the variable.',
    function () {
        $this->markTestSkipped('Not implemented yet.');
    }
);

test('Variables names and values are trimmed correctly.', function () {
    $this->markTestSkipped('Not implemented yet.');
});

test(
    'Syntax errors should send out an error and stop parsing.',
    function () {
        $this->markTestSkipped('Not implemented yet.');
    }
);

