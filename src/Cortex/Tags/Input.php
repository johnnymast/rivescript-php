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

use Axiom\Rivescript\Cortex\Input as UserInput;

/**
 * Input class
 *
 * This class is responsible parsing the <input>...<input9> tag.
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
class Input extends Tag
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
    protected string $pattern = "/<input(\d+)?>/i";

    /**
     * Parse the source.
     *
     * @param  string     $source  The string containing the Tag.
     * @param  UserInput  $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, UserInput $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $inputs = array_values(synapse()->memory->inputs()->all());

            $tags = [
                "<input>" => 0,
            ];

            for ($tagIndex = 1, $inputIndex = 0; $tagIndex < 10; $tagIndex++, $inputIndex++) {
                $tags["<input{$tagIndex}>"] = $inputIndex;
            }

            foreach ($tags as $tag => $inputIndex) {
                $reply = $inputs[$inputIndex] ?? "undefined";
                $source = str_replace($tag, $reply, $source);
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
        return ["input", "input1", "input2", "input3", "input4", "input5", "input6", "input7", "input8", "input9"];
    }
}
