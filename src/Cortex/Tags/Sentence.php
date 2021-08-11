<?php

/**
 * This class parses the {uppercase}/<uppercase> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Random class
 */
class Sentence extends Tag
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
    protected $pattern = '/({|<)sentence(}|>)(.+?)({|<)\/sentence(}|>)/u';

    /**
     * Parse the response.
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
            $matches = $this->getMatches($source)[0];

            if (isset($matches[3]) == true) {
                $found = $matches[3];
                if (strpos($found, '.') > -1) {
                    $parts = explode('.', $found);
                    if (count($parts) > 0) {
                        array_walk($parts, function (&$part) {
                            $part = ucfirst(trim($part));
                        });
                    }

                    $found = implode('.', $parts);
                } else {
                    $found = ucfirst($found);
                }

                $source = str_replace($matches[0], $found, $source);
            }
        }

        return $source;
    }
}
