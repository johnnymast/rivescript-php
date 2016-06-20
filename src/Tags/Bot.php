<?php

namespace Vulcan\Rivescript\Tags;

use Vulcan\Rivescript\Contracts\Tag;

class Bot implements Tag
{
    /**
     * @var array
     */
    protected $tree;

    /**
     * Create a new Bot instance.
     *
     * @param  array  $tree
     */
    public function __construct($tree)
    {
        $this->tree    = $tree;
        $this->pattern = regex()
            ->find('<bot ')
            ->anythingBut('>')
            ->asGroup()
            ->then('>')
            ->getRegExp();
    }

    /**
     * Parse the response.
     *
     * @param  string  $response
     * @param  array  $data
     * @return array
     */
    public function parse($response, $data)
    {
        $matches = $this->pattern->findIn($response);

        if (isset($matches[1])) {
            $response = $this->pattern->replace($response, function($match) use ($matches) {
                return $this->tree['begin']['var'][$matches[1]];
            });
        }

        return [
            'response' => $response,
            'metadata' => isset($metadata) ? $metadata : []
        ];
    }
}
