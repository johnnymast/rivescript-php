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

// FIXME Sub DESCRIPTION
// https://www.rivescript.com/wd/RiveScript#add-sub-mult-div
class Mult extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_MULT;

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

                $existingValue = synapse()->memory->user()->get($variableKey) ?? '0';
                $existingValue = ($existingValue * (int)$variableValue);

                synapse()->memory->user()->put($variableKey, $existingValue);

                $content = str_replace($variableContext, '', $value);
            }

            $command->setContent($content);
        }
    }
}