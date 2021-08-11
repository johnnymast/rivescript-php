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
     * @var array
     */
    protected $allowedSources = ['response'];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected $pattern = '/\{person\}/u';

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
            $matches = $this->getMatches($source);
            $stars = synapse()->memory->shortTerm()->get('stars');
            $substitutions = synapse()->memory->person()->all();
            $xx = synapse()->memory->variables();

            foreach ($matches as $match) {
                $index = (empty($match[1]) ? 0 : $match[1] - 1);
                $substitution = synapse()->memory->person()->get($stars[$index]) ?? 'undefined';
                $source = str_replace($match[0], $stars[$index], $source);
            }
        }

        return $source;
    }
}
