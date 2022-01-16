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
 * SpecialChars class
 *
 * This class parses the \s|\n|\/|\# characters.
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
class SpecialChars extends Tag
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
    protected string $pattern = "/(\\n|\\s|\\#|\\/)/u";

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
            $symbols = [
                '\n' => "\n",
                '\s' => ' ',
                '\/' => '/',
                '\#' => '#',
            ];

            foreach ($symbols as $symbol => $replacement) {
                $source = str_replace($symbol, $replacement, $source);
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
        return ['\n', '\s', '\/', '\#'];
    }
}
