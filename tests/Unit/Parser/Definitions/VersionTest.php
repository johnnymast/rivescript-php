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
    'An error should be sent when the script version is lower then the parser supports.',
    function () {
        $code = "! version = 1.4";

        \Mockery::close();

        $mock = \Mockery::mock(Parser::class)->makePartial();
        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::ERROR
                        && $msg->message === "Lower preferred version RiveScript version as expected. We only support :version at :filename line :lineno"
                        && $msg->args === [
                            "filename" => "stream",
                            'lineno' => 10,
                            "version" => $this->parser::RS_VERSION
                        ]
                )
            );


        $this->expectException(ParserException::class);
        $mock->parse("stream", $code);
    }
);

test(
    'An warning should be sent when the script version is higher then the parser supports.',
    function () {
        $code = "! version = " . $this->parser::RS_VERSION + 1;

        \Mockery::close();

        $mock = \Mockery::mock(Parser::class)->makePartial();
        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::WARNING
                        && $msg->message === "Higher preferred version RiveScript version as expected. This parser supports version :version at :filename line :lineno"
                        && $msg->args === [
                            "filename" => "stream",
                            'lineno' => 10,
                            "version" => $this->parser::RS_VERSION
                        ]
                )
            );

        $mock->parse("stream", $code);
    }
);