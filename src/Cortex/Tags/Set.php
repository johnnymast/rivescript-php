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
 * The Bot class is responsible for parsing the <set> and <set name=value> tags.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#get-set
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
class Set extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_SET;

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
                $variableContext = trim($match[0]);
                $variableKey = trim($match[1]);
                $variableValue = trim($match[2]);

                synapse()->memory->user()->put($variableKey, $variableValue);

                $content = str_replace($variableContext, '', $value);
            }

            $command->setContent($content);
        }
    }
}