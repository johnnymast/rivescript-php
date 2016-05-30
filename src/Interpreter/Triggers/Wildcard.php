<?php

namespace Vulcan\Rivescript\Interpreter\Triggers;

class Wildcard
{
    protected $types = [
        'alpha' => [
            'pattern'     => '/(\_)/',
            'replacement' => '[[:alpha:]]+'
        ],
        'numeric' => [
            'pattern'     => '/(\#)/',
            'replacement' => '[[:digit:]]+'
        ],
        'alphanumeric' => [
            'pattern'     => '/(\*)/',
            'replacement' => '[[:alnum:]]+'
        ]
    ];

    public function parse($trigger, $message)
    {
        foreach ($this->types as $type) {
            $parsedTrigger = preg_replace($type['pattern'], $type['replacement'], $trigger);

            if (@preg_match('#^'.$parsedTrigger.'$#', $message, $matches)) {
                return true;
            }
        }

        return false;
    }
}
