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
 * Formal class
 *
 * This class is responsible parsing the {formal}/{/formal}|<formal> tag.
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
class Formal extends Tag
{
    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = ["response", "trigger"];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected string $pattern = "/({)formal(})(.+?)({)\/formal(})|(<)formal(>)/u";

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
            $wildcards = synapse()->memory->shortTerm()->get("wildcards");

            foreach ($matches as $match) {
                if ($matches[0] === '<formal>' && is_array($wildcards) === true && count($wildcards) > 0) {
                    $sub = ucwords($wildcards[0]);
                } elseif ($matches[1] === '{' && isset($matches[3])) {
                    $sub = ucwords($matches[3]);
                } else {
                    $sub = "undefined";
                }

                $source = str_replace($matches[0], $sub, $source);
            }
        }

        return $source;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return string
     */
    public function getTagName(): string
    {
        return "formal";
    }
}
