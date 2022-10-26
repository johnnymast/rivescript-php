<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Rivescript;

uses()
    ->beforeEach(hook: function () {
        $this->rivescript = new Rivescript();
    })
    ->group('feature_tests');

test('debug() works with callback function name.', function () {


    function onDebug(string $actual): void
    {
        assertEquals("[DEBUG] Hello Function", $actual);
    }

    $this->rivescript->on(Event::DEBUG, 'onDebug');
    $this->rivescript->debug("Hello Function");
});

test('debug() works with closures.', function () {

    $expected = "[DEBUG] Hello Closure";

    $this->rivescript->on(Event::DEBUG, fn($actual) => $this->assertEquals($actual, $expected));
    $this->rivescript->debug("Hello Closure");
});

test('debug() should allow parameters', function () {
    $expected = "[DEBUG] Hello,World";
    $this->rivescript->on(Event::DEBUG, fn($actual) => $this->assertEquals($actual, $expected));
    $this->rivescript->debug(":arg1,:arg2", ['arg1' => 'Hello', 'arg2' => 'World']);
});


test('warn() works with callback function name.', function () {

    function onWarn(string $actual): void
    {
        assertEquals("[WARNING] Hello Function", $actual);
    }

    $this->rivescript->on(Event::DEBUG_WARNING, 'onWarn');
    $this->rivescript->warn("Hello Function");
});

test('warn() works with closures.', function () {

    $expected = "[WARNING] Hello Closure";

    $this->rivescript->on(Event::DEBUG_WARNING, fn($actual) => $this->assertEquals($actual, $expected));
    $this->rivescript->warn("Hello Closure");
});

test('warn() should allow parameters', function () {
    $expected = "[WARNING] Hello,World";
    $this->rivescript->on(Event::DEBUG_WARNING, fn($actual) => $this->assertEquals($actual, $expected));
    $this->rivescript->warn(":arg1,:arg2", ['arg1' => 'Hello', 'arg2' => 'World']);
});

test('say() works with callback function name.', function () {

    function onSay(string $actual): void
    {
        assertEquals("Hello Function", $actual);
    }

    $this->rivescript->on(Event::SAY, 'onSay');
    $this->rivescript->say("Hello Function");
});

test('say() works with closures.', function () {

    $expected = "Hello Closure";

    $this->rivescript->on(Event::SAY, fn($actual) => $this->assertEquals($actual, $expected));
    $this->rivescript->say("Hello Closure");
});

test('say() should allow parameters', function () {
    $expected = "Hello,World";
    $this->rivescript->on(Event::SAY, fn($actual) => $this->assertEquals($actual, $expected));
    $this->rivescript->say(":arg1,:arg2", ['arg1' => 'Hello', 'arg2' => 'World']);
});
