<?php

/**
 * Class Div handling the <div> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Class Div
 */
class Div extends Tag
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
    protected $pattern = '/<div (.+?)=(.+?)>/u';

    /**
     * Parse the response.
     *
     * @param  string  $source  The string containing the Tag.
     * @param  Input   $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, Input $input)
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source)[0];
            $userData = synapse()->memory->user($input->user())->get($matches[1]) ?? '0';

            $userData /= $matches[2];

            synapse()->memory->user($input->user())->put($matches[1], $userData);
            $source = str_replace($matches[0], '', $source);
        }

        return $source;
    }
}
