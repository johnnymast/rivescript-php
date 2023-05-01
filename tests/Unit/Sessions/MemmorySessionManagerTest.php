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

namespace Axiom\Rivescript\Tests\Unit\Sessions;

use Axiom\Rivescript\ContentLoader\Sessions;
use Axiom\Rivescript\Messages\MessageType;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\RivescriptEvent;
use Axiom\Rivescript\Sessions\MemorySessionManager;

beforeEach(
    function () {
        $this->user = "testuser";
        $this->usertwo = "usertwo";

        $this->session = new MemorySessionManager();
        $this->sessiondata = ["name" => "bob", "location" => "cloud 9"];
        $this->otherdata = ["name" => "user 2", "location" => "cloud 9"];

        $this->session->set($this->user, $this->sessiondata);
        $this->session->set($this->usertwo, $this->otherdata);
    }
);

test(
    'Frozen user variables are a snapshot and can be restored with thaw.',
    function () {
        expect($this->session->get($this->user, "name"))->toBe("bob");
        $this->session->freeze($this->user);

        $this->session->set($this->user, ["name" => "joe"]);
        expect($this->session->get($this->user, "name"))->toBe("joe");

        $this->session->thaw($this->user);
        expect($this->session->get($this->user, "name"))->toBe("bob");
    }
);

test(
    'freeze() for a unknown user should send out a warning.',
    function () {
        \Mockery::close();

        $user = "johnny";
        $mock = \Mockery::mock(MemorySessionManager::class)->makePartial();
        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::WARNING
                        && $msg->message === "Can't freeze vars for user :username : not found!"
                        && $msg->args === ["username" => $user]
                )
            );

        $mock->freeze($user);
    }
);

test(
    'get() should return user variables.',
    function () {
        expect($this->session->get($this->user, "name"))->toBe("bob");
    }
);

test(
    'get() with undefined parameter should return undefined.',
    function () {
        expect($this->session->get($this->user, "age"))->toBe("undefined");
    }
);

test(
    'getAll() should return all user variables.',
    function () {
        expect($this->session->getAll())->toEqual([
            $this->user => $this->sessiondata + $this->session->defaultSession(),
            $this->usertwo => $this->otherdata + $this->session->defaultSession()
        ]);

        $this->session->reset($this->user);

        expect($this->session->getAll())->toEqual([
            $this->usertwo => $this->otherdata + $this->session->defaultSession(),
        ]);
    }
);

test(
    'getAny() should return all variables for a given user.',
    function () {
        expect($this->session->getAny($this->user))->toEqual($this->sessiondata + $this->session->defaultSession());

        $this->session->reset($this->user);

        expect($this->session->getAny($this->user))->toBeNull();
    }
);

test(
    'reset() will reset only reset data for one user.',
    function () {
        expect($this->session->get($this->user, "name"))->toBe("bob")
            ->and($this->session->get($this->usertwo, "name"))->toBe("user 2");

        $this->session->reset($this->user);

        expect($this->session->get($this->user, "name"))->toBeNull()
            ->and($this->session->get($this->usertwo, "name"))->toBe("user 2");
    }
);

test(
    'resetAll() will reset all data.',
    function () {
        expect($this->session->get($this->user, "name"))->toBe("bob")
            ->and($this->session->get($this->usertwo, "name"))->toBe("user 2");

        $this->session->resetAll();

        expect($this->session->get($this->user, "name"))->toBeNull()
            ->and($this->session->get($this->usertwo, "name"))->toBeNull();
    }
);

test(
    'set() a value with null should unset the value.',
    function () {
        $data = $this->sessiondata;
        $data["age"] = 10;

        $this->session->set($this->user, $data);

        expect($this->session->get($this->user, "age"))->toBe(10);

        $data["age"] = null;
        $this->session->set($this->user, $data);


        expect($this->session->get($this->user, "age"))->toBe("undefined");
    }
);

test(
    'thaw() should send out a warning if there is no frozen data for the given user.',
    function () {
        \Mockery::close();
        $mock = \Mockery::mock(MemorySessionManager::class)->makePartial();

        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::WARNING
                        && $msg->message === "Can't thaw vars for user :username: not found!"
                        && $msg->args === ["username" => $this->user]
                )
            );

        $mock->set($this->user, $this->sessiondata);

        expect($mock->get($this->user, "name"))->toBe("bob");
        $mock->freeze($this->user);

        $mock->set($this->user, ["name" => "joe"]);
        expect($mock->get($this->user, "name"))->toBe("joe");

        $mock->thaw($this->user, "discard");
        $mock->thaw($this->user);
    }
);

test(
    'thaw() with action keep should restore frozen data but keep a copy.',
    function () {

        expect($this->session->get($this->user, "name"))->toBe("bob");

        $this->session->freeze($this->user);
        $this->session->set($this->user, ["name" => "joe"]);

        expect($this->session->get($this->user, "name"))->toBe("joe");

        $this->session->thaw($this->user, "keep");
        expect($this->session->get($this->user, "name"))->toBe("bob");
    }
);

test(
    'thaw() with an unsupported action should send out a warning.',
    function () {
        \Mockery::close();
        $mock = \Mockery::mock(MemorySessionManager::class)->makePartial();

        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg
                    ) => $msg->type === MessageType::WARNING && $msg->message === "Unsupported thaw action"
                )
            );

        $mock->set($this->user, $this->sessiondata);
        $mock->freeze($this->user);

        $mock->thaw($this->user, "unsupported");
    }
);

test(
    'thaw() for a unknown user should send out a warning.',
    function () {
        \Mockery::close();
        $mock = \Mockery::mock(MemorySessionManager::class)->makePartial();

        $mock->shouldReceive('emit')
            ->once()
            ->with(
                RivescriptEvent::OUTPUT,
                \Mockery::on(
                    fn(RivescriptMessage $msg) => $msg->type === MessageType::WARNING
                        && $msg->message === "Can't thaw vars for user :username: not found!"
                        && $msg->args === ["username" => $this->user]
                )
            );

        $mock->thaw($this->user);
    }
);

test(
    'defaultSession() should return the default session data.',
    function () {
        expect($this->session->defaultSession())->toEqual([
            "topic" => "random"
        ]);
    }
);
