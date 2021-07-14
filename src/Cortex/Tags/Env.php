<?php

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

class Env extends Tag
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
    protected $pattern = '/<env (.+?)>/i';

    /**
     * Parse the source.
     *
     * @param string $source
     *
     * @return string
     */
    public function parse($source, Input $input)
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source);
            $variables = synapse()->memory->global();

            foreach ($matches as $match) {
                $value = (isset($variables[$match[1]]) == true) ? $variables[$match[1]] : 'undefined';
                $source = str_replace($match[0], $value, $source);
            }
        }

        return $source;
    }
}
