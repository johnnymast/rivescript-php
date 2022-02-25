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

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Add class
 *
 * This class is responsible parsing the (@array) or @array tag.
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
class ArrayTag extends Tag
{
    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = ["trigger", "response"];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected string $pattern = "/\(@(.+?)\b\)/ui";

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

            foreach ($matches as $match) {
                $name = $match[1];


                if ($this->sourceType == "response") {
                    $array = synapse()->memory->arrays()->get($name);
                    if (is_array($array) && count($array) > 0) {
                        $rnd = array_rand($array, 1);
                        $source = str_replace($match[0], $array[$rnd], $source);
                    }
                } else {

                    if (($array = synapse()->memory->arrays()->get($name))) {
                        $wildcard = (strpos($source, "(@{$name})") > -1);

                        if ($wildcard === true) {
                            array_walk($array, 'preg_quote');

                            /**
                             * Find the match
                             */
                            $regex = "(" . implode('|', $array) . ")";
                            if (@preg_match_all('/' . $regex . '/ui', $input->source(), $wildcards)) {
                                array_shift($wildcards);

                                if ($wildcards) {
                                    $wildcards = Collection::make($wildcards)->flatten()->all();
                                    synapse()->memory->shortTerm()->put('wildcards', $wildcards);

                                    foreach ($wildcards as $wildcard) {
                                        $source = str_replace("(@{$name})", $wildcard, $source);
                                    }
                                }
                            }
                        } else {
                            /**
                             * Find the match
                             */
                            $regex = "(?:" . implode('|', $array) . ")";

                            if (@preg_match_all('/' . $regex . '/ui', $source, $results)) {
                                foreach ($results as $result) {
                                    $source = str_replace("@{$name}", $result[0], $source);
                                }
                                return $source;
                            }
                        }
                    }
                }
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
        return "array";
    }
}
