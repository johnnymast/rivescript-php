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
class Sentence extends Tag
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
    protected $pattern = '/({)sentence(})(.+?)({)\/sentence(})|(<)sentence(>)/u';

    /**
     * Parse the source.
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

            if ($matches[0] === '<sentence>' and is_array($wildcards) === true and count($wildcards) > 0) {
                $sub = $wildcards[0];
            } elseif ($matches[1] === '{' && isset($matches[3])) {
                $sub = $matches[3];
            } else {
                $sub = 'undefined';
            }

            if ($sub !== 'undefined') {
                if (strpos($sub, '.') > -1) {
                    $parts = explode('.', $sub);
                    if (count($parts) !== 0) {
                        array_walk($parts, function (&$part) {
                            $part = ucfirst(trim($part));
                        });
                    }

                    $sub = implode('.', $parts);
                } else {
                    $sub = ucfirst($sub);
                }
            }

            $source = str_replace($matches[0], $sub, $source);
        }

        return $source;
    }
}
