<?php

namespace Axiom\Rivescript;

enum RivescriptEvent: string
{
    case OUTPUT = 'output';
    case DEBUG = 'debug';
    case VERBOSE = 'verbose';
    case WARNING = 'warning';
    case ERROR = 'error';
    case SAY = 'say';
}