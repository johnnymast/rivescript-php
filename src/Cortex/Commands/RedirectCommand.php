<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Cortex\RegExpressions;

/**
 * RedirectCommand class
 *
 * Description:
 *
 * This handle and validate the command type "redirect".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#REDIRECT
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class RedirectCommand extends Command
{
    /**
     * Check the syntax for redirects.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {
        if ($this->getNode()->getTag() === '@') {
            $utf8 = synapse()->rivescript->utf8;
            $value = $this->getNode()->getValue();
            if ($utf8 === true) {
                if ($this->matchesPattern(RegExpressions::REDIRECT_SYNTAX1, $value) === true) {
                    $this->addSyntaxError(
                        "RedirectCommand can't contain uppercase letters, backslashes or dots in UTF-8 mode."
                    );
                }
            } elseif ($this->matchesPattern(RegExpressions::REDIRECT_SYNTAX2, $value) === true) {
                $this->addSyntaxError(
                    "RedirectCommand may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > ="
                );
            }

            $parens = 0; # Open parenthesis
            $square = 0; # Open square brackets
            $curly = 0; # Open curly brackets
            $chevron = 0; # Open angled brackets
            $len = strlen($value);

            for ($i = 0; $i < $len; $i++) {
                $chr = $value[$i];

                # Count brackets.
                if ($chr === '(') {
                    $parens++;
                }
                if ($chr === ')') {
                    $parens--;
                }
                if ($chr === '[') {
                    $square++;
                }
                if ($chr === ']') {
                    $square--;
                }
                if ($chr === '{') {
                    $curly++;
                }
                if ($chr === '}') {
                    $curly--;
                }
                if ($chr === '<') {
                    $chevron++;
                }
                if ($chr === '>') {
                    $chevron--;
                }
            }

            if ($parens) {
                $this->addSyntaxError(
                    "Unmatched " . ($parens > 0 ? "left" : "right") . " parenthesis bracket ()"
                );
            }
            if ($square) {
                $this->addSyntaxError(
                    "Unmatched " . ($square > 0 ? "left" : "right") . " square bracket []"
                );
            }
            if ($curly) {
                $this->addSyntaxError(
                    "Unmatched " . ($curly > 0 ? "left" : "right") . " curly bracket {}"
                );
            }
            if ($chevron) {
                $this->addSyntaxError(
                    "Unmatched " . ($chevron > 0 ? "left" : "right") . " angled bracket <>"
                );
            }
        }

        return $this->isSyntaxValid();
    }

    public function detect(): bool
    {
        // TODO: Implement parse() method.
        return false;
    }

}