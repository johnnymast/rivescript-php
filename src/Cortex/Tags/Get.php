<?php

/**
 * This class parses the <get> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Class Get
 */
class Get extends Tag
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
    protected $pattern = '/<get (.+?)>/u';

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
            $userData = synapse()->memory->user($input->user())->get($matches[1]) ?? 'undefined';

            $source = str_replace($matches[0], $userData, $source);
        }

        return $source;
    }
}
