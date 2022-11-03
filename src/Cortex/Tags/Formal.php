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

/**
 * Formal class
 *
 * Description:
 *
 * Formalize A String Of Text (Capitalize Every First Letter Of Every Word).
 *
 * + my name is *
 * - Nice to meet you, <formal>.
 *
 * <formal> is an alias for {formal}<star>{/formal}.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#formal-...-formal-formal
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
class Formal extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_FORMAL;

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
                $text = $match[0];

                if ($hasCurly) {
                    $context = ucwords(trim($match[3]));
                    $content = str_replace($text, $context, $value);

                } else {
                    /**
                     * @var ResponseAbstract $response
                     */
                    $response = $command;
                    $trigger = $response->getTrigger();


                    if ($trigger->hasStars()) {
                        if ($trigger->stars->has('<star>')) {
                            $replacement = $trigger->stars->get('<star>');
                            $content = str_replace($text, $replacement, $value);
                        }
                    }
                }

            }
        }
        $command->setContent($content);
    }
}