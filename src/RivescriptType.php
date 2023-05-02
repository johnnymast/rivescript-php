<?php

/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript;

enum RivescriptType: string
{
    case LABEL_OPEN = '>';
    case LABEL_CLOSE = '<';
    case DEFINITION = '!';
    case TRIGGER = '+';
    case RESPONSE = '-';
    case PREVIOUS = '%';
    case REDIRECT = '@';
    case CONTINUE = '^';
    case COMMENT = '/';
    case CONDITION = '*';
    case UNKNOWN = 'unknown';

    /**
     * Get the type from a code.
     *
     * @param string $code The code to convert into a value.
     *
     * @return \Axiom\Rivescript\RivescriptType
     */
    public static function fromCode(string $code): RivescriptType
    {
        return match ($code) {
            '>' => self::LABEL_OPEN,
            '<' => self::LABEL_CLOSE,
            '!' => self::DEFINITION,
            '+' => self::TRIGGER,
            '-' => self::RESPONSE,
            '%' => self::PREVIOUS,
            '@' => self::REDIRECT,
            '^' => self::CONTINUE,
            '/' => self::COMMENT,
            '*' => self::CONDITION,
            default => self::UNKNOWN
        };
    }
}
