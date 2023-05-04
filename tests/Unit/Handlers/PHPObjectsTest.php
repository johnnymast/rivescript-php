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

namespace Axiom\Rivescript\Tests\Unit\Handlers;


use Axiom\Rivescript\Rivescript;

beforeEach(
    function () {
        $this->rivescript = new Rivescript();
    }
);

test(
    'Code from text is executed',
    function () {
        $expected = "World Hello";
        $param1 = "World ";
        $param2 = "Hello";
        $routine = "string_test";

        /**
         * Set the subroutine.
         */
        $this->rivescript->setSubroutine(
            $routine,
            'function func($param1, $param2)
                 {
                    return $param1 . $param2;
                 };
                 return func("' . $param1 . '", "' . $param2 . '");'
        );

        /**
         * Call the handler.
         */
        $result = $this->rivescript->getHandler("php")->call($this->rivescript, $routine, [$param1, $param2]);
        expect($result)->toBe($expected);
    }
);

test(
    'Code from closures is executed.',
    function () {
        $expected = "Hello World";
        $param1 = "Hello ";
        $param2 = "World";
        $routine = "closure_test";

        /**
         * Set the subroutine.
         */
        $this->rivescript->setSubroutine(
            $routine,
            function ($p1, $p2) use (&$executed, &$argsIsArray, &$expected) {
                return $p1 . $p2;
            }
        );

        /**
         * Call the handler.
         */
        $result = $this->rivescript->getHandler("php")->call($this->rivescript, $routine, [$param1, $param2]);
        expect($result)->toBe($expected);
    }
);