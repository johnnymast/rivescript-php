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
use Axiom\Rivescript\Cortex\Commands\ResponseAbstract;
use Axiom\Rivescript\Cortex\RegExpressions;

// FIXME ADD GOOD DESCRIPTION
// https://www.rivescript.com/wd/RiveScript#lowercase-...-lowercase-lowercase
class Lowercase extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_LOWERCASE;

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
                $hasCurly = ($match[1] === '{');

                if ($hasCurly) {
                    $context = trim($match[3]);
                    $replacement = strtolower($context);
                    $content = str_replace($context, $replacement, $value);
                } else {
                    /**
                     * @var ResponseAbstract $response
                     */
                    $response = $command;
                    $trigger = $response->getTrigger();

                    if ($trigger->hasStars()) {
                        if ($trigger->stars->has('<star>')) {
                            $context = $trigger->stars->get('<star>');
                            $replacement = strtolower($context);

                            $content = str_replace($match[0], $replacement, $value);
                        }
                    }
                }
            }

            $command->setContent($content);
        }
    }
}