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
 * Random class
 *
 * This class parses the {random}/{random} tag.
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
class Random extends Tag
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
    protected string $pattern = "/\{random\}(.+?){\/random\}/u";

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

        $data = $source;

        while ($this->hasMatches($data)) {
            $matches = $this->getMatches($data)[0];

            if (isset($matches[1]) === true) {
                $found = $matches[0];

                if (strpos($matches[1], '|')) {
                    $separator = '|';
                } elseif (strpos($matches[1], ' ')) {
                    $separator = ' ';
                } else {
                    return $source;
                }

                $words = explode($separator, $matches[1]);

                if (count($words) !== 0) {
                    $rnd = array_rand($words);
                    $data = str_replace($found, $words[$rnd], $data);
                }
            }
        }

        return $data;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return string
     */
    public function getTagName(): string
    {
        return "random";
    }
}
