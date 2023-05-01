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

use Axiom\Rivescript\Exceptions\Parser\ParserException;
use Axiom\Rivescript\Messages\MessageType;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\Parser\Parser;
use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\RivescriptEvent;

beforeEach(function () {
    $this->parser = new Parser(new Rivescript());
});

test(
    'Variables are parsed correctly.',
    function () {
        $code = "! array colors = red green blue cyan magenta yellow black white orange brown";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["array"];

        expect($variables)->toHaveKey("colors")
            ->and($variables["colors"])->toBe("red green blue cyan magenta yellow black white orange brown");
    }
);

test(
    'Spaces are not required between the name, the value and the equal sign.',
    function () {
        $code = "! array whatis=what is|what are|what was|what were";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["array"];

        expect($variables)->toHaveKey("whatis")
            ->and($variables["whatis"])->toBe("what is|what are|what was|what were");
    }
);

test(
    'HTML can be in variable values.',
    function () {
        $code = "! array be = i will <i>be</i> here";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["array"];

        expect($variables)->toHaveKey("be")
            ->and($variables["be"])->toBe("i will <i>be</i> here");
    }
);

test(
    'A array variable named array should be allowed.',
    function () {
        $code = "! array array = one two";

        $result = $this->parser->parse("stream", $code);
        $variables = $result["begin"]["array"];

        expect($variables)->toHaveKey("array")
            ->and($variables["array"])->toBe("one two");
    }
);

test(
    'Piped arrays can\'t begin with a |',
    function () {
        \Mockery::close();
        $code = "! array key = |b";
        $mock = \Mockery::mock(Parser::class)->makePartial();
        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::ERROR
                        && $msg->message === "Piped arrays can't begin or end with a |"
                        && $msg->args === [
                            'filename' => 'stream',
                            'lineno' => 10
                        ]
                )
            );

        $this->expectException(ParserException::class);
        $mock->parse("stream", $code);
    }
);

test(
    'Piped arrays can\'t end with a |',
    function () {
        \Mockery::close();
        $code = "! array key = b|";
        $mock = \Mockery::mock(Parser::class)->makePartial();
        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::ERROR
                        && $msg->message === "Piped arrays can't begin or end with a |"
                        && $msg->args === [
                            'filename' => 'stream',
                            'lineno' => 10
                        ]
                )
            );

        $this->expectException(ParserException::class);
        $mock->parse("stream", $code);
    }
);


test(
    'Piped arrays can\'t include blank entries',
    function () {
        \Mockery::close();
        $code = "! array key = a||b";
        $mock = \Mockery::mock(Parser::class)->makePartial();
        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::ERROR
                        && $msg->message === "Piped arrays can't contain blank entries"
                        && $msg->args === [
                            'filename' => 'stream',
                            'lineno' => 10
                        ]
                )
            );

        $this->expectException(ParserException::class);
        $mock->parse("stream", $code);
    }
);

test(
    'Syntax errors should send out an error and stop parsing.',
    function () {
        \Mockery::close();
        $code = "! array key = ||"; // Create a random syntax error.

        $this->expectException(ParserException::class);
        $this->parser->parse("stream", $code);
    }
);