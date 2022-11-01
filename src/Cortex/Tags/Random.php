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
 * Random class
 *
 * The Random class is responsible for parsing the {random}...{/random} tags.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#random-...-random
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
class Random extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_RANDOM;

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {

            $matches = $this->getMatches($command->getNode());
            $content = $command->getNode()->getContent();
            $delimiters = [' ', '|'];
            $allWords = [];

            foreach ($matches as $match) {
                [$text, $context] = $match;

                $words = [$context];

                foreach ($delimiters as $delimiter) {
                    if (strchr($context, $delimiter)) {
                        $words = explode($delimiter, $context);
                        $words = array_map(fn(string $str) => trim($str), $words);
                    }
                }

                if (count($words) !== 0) {
                    $rnd = array_rand($words);
                    $content = str_replace($text, $words[$rnd], $content);
                    $allWords = array_merge($allWords, $words);
                }
            }

            $command->setRandomWords($allWords);
            $command->setContent($content);
        }
    }
}