<?php

declare(strict_types=1);

namespace Axiom\Rivescript;

enum RivescriptEvent: string
{
    case OUTPUT = 'output';
    case CUSTOM = 'custom';
    case DEBUG = 'debug';
    case VERBOSE = 'verbose';
    case WARNING = 'warning';
    case ERROR = 'error';
    case SAY = 'say';
}