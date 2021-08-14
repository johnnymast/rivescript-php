<?php

/**
 * This class parses the <input> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as UserInput;

/**
 * Input class
 */
class Input extends Tag
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
    protected $pattern = '/<input(\d+)?>/i';

    /**
     * Parse the source.
     *
     * @param  string     $source  The string containing the Tag.
     * @param  UserInput  $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, UserInput $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $inputs = array_values(synapse()->memory->inputs()->all());

            $tags = [
                "<input>" => 0,
            ];

            for ($tagIndex = 1, $inputIndex = 0; $tagIndex < 10; $tagIndex++, $inputIndex++) {
                $tags["<input{$tagIndex}>"] = $inputIndex;
            }

            foreach ($tags as $tag => $inputIndex) {
                $reply = $inputs[$inputIndex] ?? "undefined";
                $source = str_replace($tag, $reply, $source);
            }
        }

        return $source;
    }
}
