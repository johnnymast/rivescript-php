<?php

/**
 * This class parses the {uppercase}/<uppercase> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Random class
 */
class Uppercase extends Tag
{
    /**
     * @var array
     */
    protected $allowedSources = ['response'];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected $pattern = '/({)uppercase(})(.+?)({)\/uppercase(})|(<)uppercase(>)/u';

    /**
     * Parse the response.
     *
     * @param  string  $source  The string containing the Tag.
     * @param  Input   $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, Input $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source)[0];
            $wildcards = synapse()->memory->shortTerm()->get('wildcards');

            foreach ($matches as $match) {
                if ($matches[0] == '<uppercase>' and count($wildcards) > 0) {
                    $sub = strtoupper($wildcards[0]);
                } elseif ($matches[1] == '{' && isset($matches[3])) {
                    $sub = strtoupper($matches[3]);
                } else {
                    $sub = 'undefined';
                }

                $source = str_replace($matches[0], $sub, $source);
            }
        }

        return $source;
    }
}
