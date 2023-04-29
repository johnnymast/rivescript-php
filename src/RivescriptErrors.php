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

namespace Axiom\Rivescript;

enum RivescriptErrors: string
{
    case REPLY_NOT_MATCHED = "ERR: No Reply Matched";
    case REPLY_NO_FOUND = "ERR: No Reply Found";
    case OBJECT_NOT_FOUND = "[ERR: Object Not Found]";
    case DEEP_RECURSION = "ERR: Deep Recursion Detected";
}
