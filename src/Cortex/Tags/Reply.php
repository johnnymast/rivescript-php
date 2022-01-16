<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Reply class
 *
 * This class parses the <Reply>...<Reply9> tag.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Reply extends Tag
{
    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = ["response"];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected string $pattern = "/<reply(\d+)?>/i";

    /**
     * Parse the source.
     *
     * @param string      $source The string containing the Tag.
     * @param SourceInput $input  The reply information.
     *
     * @return string
     */
    public function parse(string $source, SourceInput $input): string
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
                $rpl = $replies[$replyIndex] ?? "undefined";
                $source = str_replace($tag, $rpl, $source);
            }
        }

        return $source;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return array
     */
    public function getTagName(): array
    {
        return ["reply", "reply1", "reply2", "reply3", "reply4", "reply5", "reply6", "reply7", "reply8", "reply9"];
    }
}
