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

namespace Axiom\Rivescript\Tests\Unit\Traits;

use Axiom\Rivescript\RivescriptEvent;
use Axiom\Rivescript\Traits\EventEmitter;

beforeEach(function () {
    $this->instance = new class {
        use EventEmitter;
    };
});

it('Should allow to register and handle an event.', function () {
    $executed = false;
    $this->instance->on(RivescriptEvent::CUSTOM, function () use (&$executed) {
        $executed = true;
    });

    $this->instance->emit(RivescriptEvent::CUSTOM);

    expect($executed)->toBeTrue();
});

it('Should forward all arguments given to emit to the handler', function () {
    $args = [];
    $this->instance->on(RivescriptEvent::CUSTOM, function ($param1, $param2) use (&$args) {
        $args = [$param1, $param2];;
    });

    $this->instance->emit(RivescriptEvent::CUSTOM, "AA", "BB");
    expect($args)->toEqual(["AA", "BB"]);
});

it('Should allow for multiple events to be registered in a chain.', function () {
    $registered = [false, false];

    $this->instance->on(
        RivescriptEvent::SAY, function () use (&$registered) {
        $registered[0] = true;
    }
    )->on(RivescriptEvent::DEBUG, function () use (&$registered) {
        $registered[1] = true;
    });

    $this->instance->emit(RivescriptEvent::SAY)
        ->emit(RivescriptEvent::DEBUG);

    expect($registered)->toEqual([true, true]);
});

it('Can use functions as callbacks', function () {
    new class {

        use EventEmitter;

        protected bool $received = false;

        public function __construct()
        {
            $this->on(RivescriptEvent::CUSTOM, [$this, 'onEvent']);
            $this->emit(RivescriptEvent::CUSTOM);

            expect($this->received)->toBeTrue();
        }

        private function onEvent(): void
        {
            $this->received = true;
        }
    };
});