<?php

/**
 * This class parses the {topic} tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Topic class
 */
class Topic extends Tag
{
    /**
     * @var array<string>
     */
    protected $allowedSources = ['response'];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected $pattern = '/\{topic=(.+?)\}/u';

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
            list($find, $topic) = $this->getMatches($source)[0];

            $source = str_replace($find, '', $source);
            synapse()->memory->shortTerm()->put('topic', $topic);
        }

        return $source;
    }
}
