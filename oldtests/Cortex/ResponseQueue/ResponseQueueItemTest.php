<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Cortex\ResponseQueue;

use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem;

uses()
    ->group('responsequeue');

it('takes the values and sets them accordingly', function () {
    $command = '* some condition.';
    $type = 'type';
    $order = 10;

    $queueItem = new ResponseQueueItem($command, $type, $order);

    $expected = $command;
    $actual = $queueItem->command;
    $this->assertEquals($expected, $actual);

    $expected = $type;
    $actual = $queueItem->type;
    $this->assertEquals($expected, $actual);

    $expected = $order;
    $actual = $queueItem->order;
    $this->assertEquals($expected, $actual);
});