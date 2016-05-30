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

    public function parse($key, $trigger, $message)
    {
        foreach ($this->types as $type) {
            $parsedTrigger = preg_replace($type['pattern'], '('.$type['replacement'].')', $trigger);

            if (@preg_match('#^'.$parsedTrigger.'$#', $message, $stars)) {
                array_shift($stars);

                return [
                    'match' => true,
                    'key'   => $key,
                    'data'  => ['stars' => $stars],
                ];
            }
        }

        return ['match' => false];
    }
}
