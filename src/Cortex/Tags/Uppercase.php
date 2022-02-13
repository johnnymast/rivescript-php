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
 * Uppercase class
 *
 * This class is responsible parsing the {uppercase}{/uppercase} tag.
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
class Uppercase extends Tag
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
    protected string $pattern = "/{uppercase}(.+?){\/uppercase}|<uppercase>/u";

    /**
     * Parse the source.
     *
     * @param string      $source The string containing the Tag.
     * @param SourceInput $input  The input information.
     *
     * @return string
     */
    public function parse(string $source, SourceInput $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source);
            $wildcards = synapse()->memory->shortTerm()->get("wildcards");
            $wildcard = 0;

            foreach ($matches as $match) {
                if ($match[0] === '<uppercase>' && is_array($wildcards) === true && count($wildcards) > 0) {
                    if (isset($wildcards[$wildcard])) {
                        $sub = strtoupper($wildcards[$wildcard]);
                        $pos = strpos($source, $match[0]);
                        if ($pos !== false) {
                            $source = substr_replace($source, $sub, $pos, strlen($match[0]));
                        }

                        $wildcards = array_shift($wildcards);
                    }
                } elseif ($match[0][0] == '{' && isset($match[1])) {
                    $sub = strtoupper($match[1]);
                } else {
                    $sub = "undefined";
                }

                $source = str_replace($match[0], $sub, $source);
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
        return "uppercase";
    }
}
