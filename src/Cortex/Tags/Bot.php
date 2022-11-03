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
 * Bot class
 *
 * The Bot class is responsible for parsing the <bot> tag.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#bot
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
class Bot extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_BOT;

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
            $value = $command->getNode()->getValue();


            foreach ($matches as $match) {
                $string = $match[0];
                $isSetter = !empty($match[1]);

                if ($isSetter === false) {
                    $variableValue = "undefined";
                    $variableKey = trim($match[3]);

                    if (synapse()->memory->variables()->has($variableKey)) {
                        $variableValue = synapse()->memory->variables()->get($variableKey);
                    }

                    $content = str_replace($string, $variableValue, $value);
                } else {
                    $variableValue = trim($match[2]);
                    $variableKey = trim($match[1]);

                    synapse()->memory->variables()->put($variableKey, $variableValue);

                    $content = str_replace($string, '', $value);
                }
            }

//            $command->getNode()->setValue($value);
            $command->setContent($content);
        }
    }
}