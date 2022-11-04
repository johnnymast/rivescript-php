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

/**
 * Chars class
 *
 * The Chars class is responsible for parsing the \s\n\\\\n and \# tags.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#s
 * @see      https://www.rivescript.com/wd/RiveScript#n
 * @see      https://www.rivescript.com/wd/RiveScript#pod2
 * @see      https://www.rivescript.com/wd/RiveScript#pod3
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
class Chars extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_CHARS;

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {
            $content = $command->getNode()->getContent();


            $symbols = [
                '\n' => "\n",
                '\s' => ' ',
                '\/' => '/',
                '\#' => '#',
            ];

            foreach ($symbols as $symbol => $replacement) {
                $content = str_replace($symbol, $replacement, $content);
            }

            $command->setContent($content);
        }
    }
}