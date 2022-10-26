<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cortex\MiniStack;

use Axiom\Rivescript\Cortex\MiniStack\MiniStack;

uses()
    ->group('unit_tests');

it("constructs correct", function () {
    $expected = 5;
    $stack = new MiniStack($expected);
    $actual = $stack->getSize();

    $this->assertEquals($expected, $actual);
});

it("can only contain as much items as the constructor dictates", function () {
    $expected = 3;
    $stack = new MiniStack($expected);
    for ($i = 1; $i < ($expected + 1); $i++) {
        $stack->push($i);
    }

    $stack->push('next');
    $stack->push('next2');

    $actual = $stack->count();
    $this->assertEquals($expected, $actual);
});

it("forgets items older the then indicated size", function () {
    $size = 5;
    $stack = new MiniStack($size);

    for ($i = 1; $i < ($size + 1); $i++) {
        $stack->push("item{$i}");
    }

    $expected = [
        'item3',
        'item4',
        'item5',
        'next1',
        'next2',
    ];

    $stack->push("next1");
    $stack->push("next2");

    $this->assertEquals($expected, array_values($stack->all()));
});
