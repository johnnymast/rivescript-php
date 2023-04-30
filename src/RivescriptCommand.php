<?php

namespace Axiom\Rivescript;

enum RivescriptCommand: string
{
    case LABEL_OPEN = '>';
    case LABEL_CLOSE = '<';
    case DEFINITION = '!';
    case TRIGGER = '+';

    case UNKNOWN = 'unknown';

    public static function fromCode(string $cmd): RivescriptCommand
    {
        return match ($cmd) {
            '>' => self::LABEL_OPEN,
            '<' => self::LABEL_CLOSE,
            '!' => self::DEFINITION,
            '+' => self::TRIGGER,
            default => self::UNKNOWN
        };
    }
}
