<?php

/**
 * This class parses the {lowercase}/<lowercase> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Random class
 */
class Lowercase extends Tag
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
    protected $pattern = '/({)lowercase(})(.+?)({)\/lowercase(})|(<)lowercase(>)/u';

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
            $matches = $this->getMatches($source)[0];
            $wildcards = synapse()->memory->shortTerm()->get('wildcards');

            foreach ($matches as $match) {
                if ($matches[0] === '<lowercase>' and is_array($wildcards) === true and count($wildcards) > 0) {
                    $sub = strtolower($wildcards[0]);
                } elseif ($matches[1] === '{' && isset($matches[3])) {
                    $sub = strtolower($matches[3]);
                } else {
                    $sub = 'undefined';
                }

                $source = str_replace($matches[0], $sub, $source);
            }
        }

        return $source;
    }
}
