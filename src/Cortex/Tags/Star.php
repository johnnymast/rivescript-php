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
use function _PHPStan_71ced81c9\RingCentral\Psr7\str;

/**
 * Star class
 *
 * The start tag matches the wildcards in the response
 * and replaces them with the words from the trigger.
 *
 * Example:
 *
 * + my name is *
 * - hello <star>, <star1> is a nice name.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#star-star1---starN
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Star extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_STAR;

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {

            /**
             * @var \Axiom\Rivescript\Cortex\Commands\ResponseAbstract $command ;
             */
            $trigger = $command->getTrigger();
            $value = $command->getNode()->getValue();

            $matches = $this->getMatches($command->getNode());

            foreach ($matches as $match) {
                $key = $match[0];
                if ($trigger->stars->has($key)) {
                    $replacement = $trigger->stars->get($key);
                    $value = str_replace($key, $replacement, $value);
                }
            }

            $command->getNode()->setValue($value);
        }
    }
}