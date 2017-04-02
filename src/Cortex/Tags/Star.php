<?php

namespace Vulcan\Rivescript\Cortex\Tags;

use Vulcan\Rivescript\Contracts\Tag;

class Star implements Tag
{
    /**
     * Regex expression pattern.
     *
     * @var string
     */
    public $pattern = '/<star(([0-9])?)>/i';

    /**
     * Parse the response.
     *
     * @param  string  $response
     * @param  array  $data
     * @return array
     */
    public function parse($source)
    {
        return $source;
    }
}
