<?php

/**
 * This class parses the <star> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Class Star
 */
class Star extends Tag
{
    /**
     * @var array<string>
     */
    protected $allowedSources = ['response', 'trigger'];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected $pattern = '/<star(\d+)?>/i';

    /**
     * Parse the source.
     *
     * @param  string       $source  The string containing the Tag.
     * @param  SourceInput  $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, SourceInput $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source);
            $wildcards = synapse()->memory->shortTerm()->get('wildcards');

            foreach ($matches as $match) {
                $index = (empty($match[1]) ? 0 : $match[1] - 1);
                $source = str_replace($match[0], $wildcards[$index], $source);
            }
        }

        return $source;
    }
}
