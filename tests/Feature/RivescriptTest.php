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

namespace Axiom\Rivescript\Tests\Feature;

use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\RivescriptEvent;

beforeEach(function () {
    $this->rivescript = new Rivescript();
    $this->user = "testuser";
});

test(
    'values set by setUserVars() should be retrievable by getUserVar().',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'joe');

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('joe');
    }
);

test(
    'setUserVars() should work for a single user.',
    function () {
        $this->rivescript->setUserVars($this->user, ['name' => 'joe', 'age' => 20]);

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('joe')
            ->and($this->rivescript->getUserVar($this->user, 'age'))->toBe(20);
    }
);

test(
    'setUserVars() should work for a multiple users.',
    function () {
        $this->rivescript->setUserVars(null, [
            $this->user => ['name' => 'joe', 'age' => 20],
            'otheruser' => ['name' => 'jane', 'age' => 30],
        ]);

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('joe')
            ->and($this->rivescript->getUserVar($this->user, 'age'))->toBe(20)
            ->and($this->rivescript->getUserVar("otheruser", 'name'))->toBe('jane')
            ->and($this->rivescript->getUserVar("otheruser", 'age'))->toBe(30);
    }
);

test(
    'setUserVars() should work for a multiple users with named arguments.',
    function () {
        $this->rivescript->setUserVars(data: [
            $this->user => ['name' => 'jack', 'age' => 18],
            'otheruser' => ['name' => 'chuck', 'age' => 15],
        ]);

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('jack')
            ->and($this->rivescript->getUserVar($this->user, 'age'))->toBe(18)
            ->and($this->rivescript->getUserVar("otheruser", 'name'))->toBe('chuck')
            ->and($this->rivescript->getUserVar("otheruser", 'age'))->toBe(15);
    }
);

test(
    'setUserVar() should set a user variable for a single user.',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'steph');
        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('steph');
    }
);

test(
    'getUserVars() should return all user variables for a single user.',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'Jenny');
        $this->rivescript->setUserVar($this->user, 'age', 26);

        $expected = [
            "topic" => "random", // This is a default variable.
            "name" => "Jenny",
            "age" => 26
        ];

        $actual = $this->rivescript->getUserVars($this->user);
        expect($actual)->toBe($expected);
    }
);

test(
    'getUserVars() should return all user variables for a all users if username is not passed.',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'Jenny');
        $this->rivescript->setUserVar($this->user, 'age', 26);

        $this->rivescript->setUserVar("otheruser", 'name', 'Bob');
        $this->rivescript->setUserVar("otheruser", 'age', 30);

        $expected = [
            $this->user => [
                "topic" => "random", // This is a default variable.
                "name" => "Jenny",
                "age" => 26
            ],
            "otheruser" => [
                "topic" => "random", // This is a default variable.
                "name" => "Bob",
                "age" => 30
            ]
        ];

        $actual = $this->rivescript->getUserVars();
        expect($actual)->toBe($expected);
    }
);

test(
    'clearUserVars() should clear the user variables for a single user.',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'steph');
        $this->rivescript->setUserVar("otheruser", 'name', 'bob');

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('steph')
            ->and($this->rivescript->getUserVar("otheruser", 'name'))->toBe('bob');

        $this->rivescript->clearUserVars($this->user);

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBeNull()
            ->and($this->rivescript->getUserVar("otheruser", 'name'))->toBe('bob');
    }
);

test(
    'clearUserVars() should clear the user variables for all users if username if not given.',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'steph');
        $this->rivescript->setUserVar("otheruser", 'name', 'bob');

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBe('steph')
            ->and($this->rivescript->getUserVar("otheruser", 'name'))->toBe('bob');

        $this->rivescript->clearUserVars();

        expect($this->rivescript->getUserVar($this->user, 'name'))->toBeNull()
            ->and($this->rivescript->getUserVar("otheruser", 'name'))->toBeNull();
    }
);

test(
    'freezeUserVars() should freeze the user variables and thawUserVars() will unfreeze them.',
    function () {
        $this->rivescript->setUserVar($this->user, 'name', 'steph');
        expect($this->rivescript->getUserVar($this->user, "name"))->toBe("steph");

        $this->rivescript->freezeUserVars($this->user);

        $this->rivescript->setUserVar($this->user, 'name', 'joe');
        expect($this->rivescript->getUserVar($this->user, "name"))->toBe("joe");

        $this->rivescript->thawUserVars($this->user);

        $this->rivescript->setUserVar($this->user, 'name', 'steph');
        expect($this->rivescript->getUserVar($this->user, "name"))->toBe("steph");
    }
);

test(
    'debug() should output debug messages',
    function () {
        $actual = "";
        $expected = "[DEBUG] This is a debug message";

        $this->rivescript->on(
            RivescriptEvent::DEBUG,
            function (string $msg) use (&$actual) {
                $actual = $msg;
            }
        );

        $this->rivescript->debug("This is a :type message", ['type' => 'debug']);
        expect($actual)->toBe($expected);
    }
);

test(
    'verbose() should output verbose messages',
    function () {
        $actual = "";
        $expected = "[VERBOSE] This is a verbose message";

        $this->rivescript->on(
            RivescriptEvent::VERBOSE,
            function (string $msg) use (&$actual) {
                $actual = $msg;
            }
        );

        $this->rivescript->verbose("This is a :type message", ['type' => 'verbose']);
        expect($actual)->toBe($expected);
    }
);

test(
    'warn() should output warning messages',
    function () {
        $actual = "";
        $expected = "[WARNING] This is a warning message";

        $this->rivescript->on(
            RivescriptEvent::WARNING,
            function (string $msg) use (&$actual) {
                $actual = $msg;
            }
        );

        $this->rivescript->warn("This is a :type message", ['type' => 'warning']);
        expect($actual)->toBe($expected);
    }
);

test(
    'error() should output warning messages',
    function () {
        $actual = "";
        $expected = "[ERROR] This is a error message";

        $this->rivescript->on(
            RivescriptEvent::ERROR,
            function (string $msg) use (&$actual) {
                $actual = $msg;
            }
        );

        $this->rivescript->error("This is a :type message", ['type' => 'error']);
        expect($actual)->toBe($expected);
    }
);

test(
    'say() should output a message',
    function () {
        $actual = "";
        $expected = "I just say \"Hello World\"";

        $this->rivescript->on(
            RivescriptEvent::SAY,
            function (string $msg) use (&$actual) {
                $actual = $msg;
            }
        );

        $this->rivescript->say("I just say :param", ['param' => '"Hello World"']);
        expect($actual)->toBe($expected);
    }
);


test(
    'sortReplies() should sort the replies by their weights.',
    function () {
        $this->markTestSkipped('This test has not been implemented yet.');
    }
);

test(
    'setHandler() should register a new handler for a programming language.',
    function () {
        $this->markTestSkipped('This test has not been implemented yet.');
    }
);

test(
    'setHandler() should remove a new handler for a programming language if the object is null.',
    function () {
        $this->markTestSkipped('This test has not been implemented yet.');
    }
);

test(
    'stream() xxx.',
    function () {
        $this->markTestSkipped('This test has not been implemented yet.');
    }
);