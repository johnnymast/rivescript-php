<?php

namespace Axiom\Rivescript\Parser;

enum ParseResultStatus: int
{
    case ERROR = 0;
    case SUCCESS = 1;
    case WARNING = 2;
    case SYNTAX_ERROR = 3;
}