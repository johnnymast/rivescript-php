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
 * Star class
 *
 * The start tag matches the wildcards in the response
 * and replaces them with the words from the triggerr.
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
     * Star signs arnt they nice :)
     *
     * @var array
     */
    protected array $stars = [
        ['star', 'star1'],
        'star2',
        'star3',
        'star4',
        'star5',
        'star6',
        'star7',
        'star8',
        'star9'
    ];

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
        // FIXME BUG IF STAR VALUE HES A SPACE like "you are" it will return you. Example cae ! person you are

        if ($this->isSourceOfType(self::RESPONSE)) {

            /**
             * @var \Axiom\Rivescript\Cortex\Commands\ResponseCommand $command ;
             */
            $trigger = $command->getTrigger();
            $wildcards = $trigger->getWildcards();
            $input = synapse()->input->source();
            $node = $trigger->getNode()->getValue();

            /**
             * Wild cards are stored ordered by position ascending
             * but if we replace below from the start of the string to
             * the end it ends with invalid results. So reverse te array
             * and replace from the end of the node to the front.
             */
            $wildcards = array_reverse($wildcards);
            foreach ($wildcards as $wildcard) {
                $position = $wildcard->getStringPosition();
                $node = substr_replace($node, $wildcard->getTag(), $position, strlen($wildcard->getCharacter()));
            }

            $triggerParts = explode(' ', $node);
            $inputParts = explode(' ', $input);

            $diff = array_diff($triggerParts, $inputParts);
            $result = [];

            foreach ($diff as $key => $value) {
                $noColon = substr($value, 1);
                $result[$noColon] = $inputParts[$key];
            }

            $values = array_values($result);
            $replacements = [];

            foreach ($this->stars as $index => $star) {
                if (isset($values[$index])) {
                    if ($index == 0 && is_array($star) === true) {
                        foreach ($star as $name) {
                            $replacements[$name] = $values[$index];
                        }
                    } else {
                        $replacements[$star] = $values[$index];
                    }
                }
            }

            // i hate red but i like blue
            $content = Misc::formatString($node, $result);
            $command->setContent($content);
        }
    }
}