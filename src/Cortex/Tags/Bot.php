<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * Bot class
 *
 * This class is responsible parsing the <bot> tag.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Bot extends Tag
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
    protected string $pattern = "/<bot (.+?)=(.+?)>\b|<bot (.+?)>/u";

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
            $variables = synapse()->memory->variables();

            foreach ($matches as $match) {
                [$string, $key, $value] = $match;

                if (isset($match[3])) {
                    $key = $match[3];
                }

                if (empty($value) === false) {
                    $value = str_replace(["&#60;", "&#62;"], ["<", ">"], $value);

                    synapse()->memory->variables()->put($key, $value);
                    $source = str_replace($string, '', $source);
                } else {
                    $source = str_replace($string, $variables[$key] ?? "undefined", $source);
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
        return "bot";
    }
}
