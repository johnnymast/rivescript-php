<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Traits;

/**
 * Tags trait
 *
 * A collection of tag helpers.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Traits
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
trait Tags
{
    /**
     * Parse the response through the available Tags.
     *
     * @param string $source The response string to parse.
     *
     * @return string
     */
    protected function parseTags(string $source): string
    {

        $tags = synapse()->memory->tags();
        $source = $this->escapeUnknownTags($source);

        foreach ($tags as $tag) {
            $source = $tag->parse($source, synapse()->input);
        }

        $source = str_replace(["&#60;", "&#62;"], ["<", ">"], $source);

        return $source;
        return trim($source);
    }

    /**
     * Escape unknown Tags, so they don't get picked up by the parser
     * later on in the process.
     *
     * @param string $source The source to escape.
     *
     * @return string
     */
    public function escapeUnknownTags(string $source): string
    {

        $knownTags = synapse()->memory->tags()->keys()->all();

        $pattern = '/<(\S*?)*>.*?<\/\1>/s';

        preg_match_all($pattern, $source, $matches);

        $index = 0;
        if (is_array($matches[$index]) && isset($matches[$index][0]) && is_null($knownTags) === false && count($matches) == 2) {
            $matches = $matches[$index];

            foreach ($matches as $match) {
                $str = str_replace(['<', '>'], ["&#60;", "&#62;"], $match);
                $parts = explode(' ', $str);
                $tag = $parts[0] ?? "";

                if (in_array($tag, $knownTags, true) === false) {
                    $source = str_replace($match, $str, $source);
                }
            }
        }

        return $source;

    }
}
