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
 * Person class
 *
 * This class parses the {person}/{/person}, <person> tag.
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
class Person extends Tag
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
    protected string $pattern = "/({)person(})(.+?)({)\/person(})|(<)person(>)/u";

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
                $wildcards = synapse()->memory->shortTerm()->get("wildcards");

                $patterns = synapse()->memory->person()->keys()->all();
                $replacements = synapse()->memory->person()->values()->all();

                $sub = "";

                foreach ($patterns as $index => $pattern) {
                    $patterns[$index] = "/\b" . $pattern . "\b/i";
                }

                // TODO: Test multiple person wildcards
                if ($match[0] === '<person>' && is_array($wildcards) === true && count($wildcards) > 0) {
                    if (count($patterns) > 0) {
                        foreach ($patterns as $index => $pattern) {
                            $sub = preg_replace($pattern, $replacements[$index], $wildcards[0]);// ?? 'undefined';

                            if ($sub !== $wildcards[0]) {
                                $source = str_replace($match[0], $sub, $source);
                            }
                        }
                    } else {
                        $sub = preg_replace($patterns, $replacements, $wildcards[0], 1);// ?? 'undefined';
                        $source = str_replace($match[0], $sub, $source);
                    }
                } elseif ($match[1] === '{') {
                    $replacement = synapse()->memory->person()->get($match[3]) ?? "undefined";
                    $source = str_replace($match[0], $replacement, $source);
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
        return "person";
    }
}
