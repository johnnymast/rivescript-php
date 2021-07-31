<?php

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

class Star extends Tag
{
    /**
     * @var array
     */
    protected $allowedSources = ['response', 'trigger'];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected $pattern = '/<star(\d+)?>/i';

    /**
     * Parse the response.
     *
     * @param  string  $source  The string containing the Tag.
     * @param  Input   $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, Input $input): star
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source);
            $stars = synapse()->memory->shortTerm()->get('stars');

            foreach ($matches as $match) {
                $index = (empty($match[1]) ? 0 : $match[1] - 1);
                $source = str_replace($match[0], $stars[$index], $source);
            }
        }

        return $source;
    }
}
