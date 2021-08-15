<?php

/**
 * This class parses the \s|\n|\/|\# tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * SpecialChars class
 */
class SpecialChars extends Tag
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
    protected $pattern = '/(\\n|\\s|\\#|\\/)/u';

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
            $symbols = [
                '\n' => "\n",
                '\s' => ' ',
                '\/' => '/',
                '\#' => '#',
            ];

            foreach ($symbols as $symbol => $replacement) {
                $source = str_replace($symbol, $replacement, $source);
            }
        }

        return $source;
    }
}
