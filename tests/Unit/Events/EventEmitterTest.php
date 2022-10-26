<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Events\EventEmitter;

uses()
    ->beforeEach(function () {
        $this->instance = new class {
            use EventEmitter;
        };
    })
    ->group('unit_tests');

it('should allow to register and handle an event.', function () {
    $this->instance->on('event', function () {
        $this->assertTrue(true);
    });

    $this->instance->emit('event');
});

it('should forward all arguments given to emit to the handler', function () {
    $this->instance->on('event', function ($param1, $param2) {
        $this->assertEquals("AA", $param1);
        $this->assertEquals("BB", $param2);
    });

    $this->instance->emit('event', "AA", "BB");
});

it('should allow for multiple events to be registered in chain.', function () {
    $this->instance->on('eventA', function () {
        $this->assertTrue(true);
    })
        ->on('eventB', function () {
            $this->assertTrue(true);
        });


    $this->instance->emit('eventA')
        ->emit('eventB');
});

it('can use functions as callbacks', function () {


    new class($this) {

        use EventEmitter;

        protected $pest;

        public function __construct($pest)
        {
            $this->pest = $pest;

            print_r($this->pest);

            $this->on('event', [$this, 'onEvent']);
            $this->emit('event',$this->pest);
        }

        private function onEvent($pest): void
        {
            $pest->assertTrue(true);
        }
    };
});