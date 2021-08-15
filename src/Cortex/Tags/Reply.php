<?php

/**
 * This class parses the <reply> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Reply class
 */
class Reply extends Tag
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
    protected $pattern = '/<reply(\d+)?>/i';

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
            $replies = array_values(synapse()->memory->replies()->all());

            $tags = [
                "<reply>" => 0,
            ];

            for ($tagIndex = 1, $replyIndex = 0; $tagIndex < 10; $tagIndex++, $replyIndex++) {
                $tags["<reply{$tagIndex}>"] = $replyIndex;
            }

            foreach ($tags as $tag => $replyIndex) {
                $reply = $replies[$replyIndex] ?? "undefined";
                $source = str_replace($tag, $reply, $source);
            }
        }

        return $source;
    }
}
