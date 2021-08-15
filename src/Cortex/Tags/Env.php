<?php

/**
 * Class Div handling the <env> (global variables) tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Class Env
 */
class Env extends Tag
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
    protected $pattern = '/<env (.+?)>/i';

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
            $variables = synapse()->memory->global();

            foreach ($matches as $match) {
                $value = (isset($variables[$match[1]]) == true) ? $variables[$match[1]] : 'undefined';
                $source = str_replace($match[0], $value, $source);
            }
        }

        return $source;
    }
}
