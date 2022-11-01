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

use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Utils\Misc;

/**
 * Input class
 *
 * The Input class is responsible for parsing the <input> tag.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#input1---input9-reply1---reply9
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Input extends Tag implements TagInterface
{

    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = [self::RESPONSE];

    /**
     * The pattern for this tag.
     *
     * @var string
     */
    protected string $pattern = RegExpressions::TAG_INPUT;

    /**
     * All input signs in a row.
     *
     * @var array
     */
    protected array $stars = [
        ['input', 'input1'],
        'input2',
        'input3',
        'input4',
        'input5',
        'input6',
        'input7',
        'input8',
        'input9'
    ];

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {
            $content = $command->getNode()->getContent();
            $matches = $this->getMatches($command->getNode());
            $inputs = array_values(synapse()->memory->inputs()->all());

            /**
             * First of replace all the inputs with a colon before
             * it. This is so we can use it in our format string function
             * later.
             */
            $results = [];
            foreach ($matches as $match) {
                $input = $match[0];
                $replace = str_replace(['<', '>'], ['[', ']'], $input);
                $number = (int)substr($input, -2, 1);

                if ($number < 2) {
                    $results[$replace] = $inputs[0] ?? "undefined";;
                    $content = str_replace(["<input>", "<input1>"], [":[input]", ":[input1]"], $content);
                } else {
                    $results[$replace] = $inputs[$number-1] ?? "undefined";
                    $content = str_replace("<input{$number}>", ":[input{$number}]", $content);
                }
            };

            $content = Misc::formatString($content, $results);
            $command->setContent($content);
        }
    }
}