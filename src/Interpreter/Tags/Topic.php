<?php

namespace Vulcan\Rivescript\Interpreter\Tags;

class Topic
{
    /**
     * Regex expression pattern.
     *
     * @var string
     */
    public $pattern = '/{\s*topic\s*=\s*(\w+)\s*}/i';

    /**
     * Parse the response.
     *
     * @param  string  $response
     * @param  array  $data
     * @return string
     */
    public function parse($response, $data)
    {
        preg_match_all($this->pattern, $response, $matches);

        if (! empty($matches[1])) {
            $response          = preg_replace($this->pattern, '', $response);
            $metadata['topic'] = $matches[1][0];
        }

        return [
            'response' => $response,
            'metadata' => isset($metadata) ? $metadata : []
        ];
    }
}
