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
 * Mult class
 *
 * This class is responsible parsing the <mult> tag.
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
class __Mult extends Tag
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
    protected string $pattern = "/<mult (.+?)=(.+?)>/u";

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
                $name  = $match[1];
                $value = $match[2];

                if (empty($match[3]) === false && empty($match[4]) === false) {
                    $name = $match[3];
                    $value = $match[4];
                }


                $variable = synapse()->memory->user($input->user())->get($name) ?? "undefined";
                $source = str_replace($match[0], '', $source);

                if (is_numeric($value) === false) {
                    return "[ERR: Math can't 'mult' non-numeric value '{$value}']{$source}";
                } elseif ($variable === "undefined" || is_numeric($variable) === false) {
                    return "[ERR: Math can't 'mult' non-numeric user variable '{$name}']{$source}";
                }

                $variable *= $value;
                synapse()->memory->user($input->user())->put($name, $variable);
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
        return "mult";
    }
}
