<?php

/**
 * This class parses the {person}/<person> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Class Get
 */
class Person extends Tag
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
    protected $pattern = '/({)person(})(.+?)({)\/person(})|(<)person(>)/u';

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
            $matches = $this->getMatches($source);
            $wildcards = synapse()->memory->shortTerm()->get('wildcards');

            $patterns = synapse()->memory->person()->keys()->all();
            $replacements = synapse()->memory->person()->values()->all();

            foreach ($matches as $match) {
                if ($match[0] == '<person>' and count($wildcards) > 0) {
                    $sub = preg_replace($patterns, $replacements, $wildcards[0]) ?? 'undefined';

                    /**
                     * If nothing is replaced it means
                     * no person variable has been found.
                     *
                     * Set sub (substitute) to 'undefined'
                     */
                    if ($sub === $wildcards[0]) {
                        $sub = 'undefined';
                    }
                } elseif ($match[1] == '{') {
                    $key = '/\b'.preg_quote($match[3], '/').'\b/';
                    $sub = synapse()->memory->person()->get($key) ?? 'undefined';
                } else {
                    $sub = 'undefined';
                }

                $source = str_replace($match[0], $sub, $source);
            }
        }

        return $source;
    }
}
