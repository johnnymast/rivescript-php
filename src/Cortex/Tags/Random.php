<?php

/**
 * This class parses the {random} tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Random class
 */
class Random extends Tag
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
    protected $pattern = '/\{random\}(.+?){\/random\}/u';

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

        $data = $source;

        while ($this->hasMatches($data)) {
            $matches = $this->getMatches($data)[0];

            if (isset($matches[1]) == true) {
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
}
