<?php

namespace Vulcan\Rivescript\Interpreter\Triggers;

class Wildcard
{
    protected $types = [
        'alpha',
        'numeric',
        'alphanumeric'
    ];

    public function parse($trigger, $message)
    {
        foreach ($this->types as $type) {
            if ($this->$type($trigger, $message) === true) {
                return true;
            }
        }

        return false;
    }

    protected function alpha($trigger, $message)
    {
        $pattern     = '/(\_)/';
        $replacement = '[[:alpha:]]+';

        $trigger = preg_replace($pattern, $replacement, $trigger);

        if (@preg_match('#^'.$trigger.'$#', $message, $matches)) {
            return true;
        }

        return false;
    }

    protected function numeric($trigger, $message)
    {
        $pattern     = '/(\#)/';
        $replacement = '[[:digit:]]+';

        $trigger = preg_replace($pattern, $replacement, $trigger);

        if (@preg_match('#^'.$trigger.'$#', $message, $matches)) {
            return true;
        }

        return false;
    }

    protected function alphanumeric($trigger, $message)
    {
        $pattern     = '/(\*)/';
        $replacement = '[[:alnum:]]+';

        $trigger = preg_replace($pattern, $replacement, $trigger);

        if (@preg_match('#^'.$trigger.'$#', $message, $matches)) {
            return true;
        }

        return false;
    }
}
