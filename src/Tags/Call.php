<?php

namespace Vulcan\Rivescript\Tags;

use Vulcan\Rivescript\Contracts\Tag;

class Call implements Tag
{
    /**
     * Regex expression pattern.
     *
     * @var string
     */
    public $pattern = '/<call>(.*?)<\/call>/i';

    protected $tree;

    public function __construct($tree)
    {
        $this->tree = $tree;
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
        preg_match_all($this->pattern, $response, $matches);

        if (isset($matches[1][0])) {
            $macro  = explode(' ', $matches[1][0]);
            $object = $macro[0];

            unset($macro[0]);

            $arguments = implode(' ', $macro);

            if (isset($this->tree['objects'][$object])) {
                ob_start();
                    eval($this->tree['objects'][$object]);
                    $replace = ob_get_contents();
                ob_end_clean();
            }

            $search = $matches[0][0];

            if (isset($replace)) {
                $response = str_replace($search, $replace, $response);
            }
        }

        return [
            'response' => $response,
            'metadata' => isset($metadata) ? $metadata : []
        ];
    }
}
