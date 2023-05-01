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

namespace Axiom\Rivescript\Messages;

enum MessageType: string
{
    case WARNING = 'warn';
    case INFO = 'info';
    case DEBUG = 'debug';
    case ERROR = 'error';
}
