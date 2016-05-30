<?php

namespace Vulcan\Rivescript\Tags;

class Topic
{
    /**
     * Regex expression pattern.
     *
     * @var string
     */
    public $pattern = '/{\s*topic\s*=\s*(\w+)\s*}/i';

    public function parse($text, $data)
    {
        preg_match($this->pattern, $text, $matches);

        if ($matches[0]) {
            $text          = preg_replace($this->pattern, '', $text);
            $data['topic'] = $matches[0];
        }

        return [$text, $data];
    }
}
