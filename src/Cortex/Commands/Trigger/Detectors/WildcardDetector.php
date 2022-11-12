<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands\Trigger\Detectors;

use Axiom\Rivescript\Cortex\Attributes\TriggerDetector;
use Axiom\Rivescript\Cortex\Commands\TriggerCommand;
use Axiom\Rivescript\Cortex\Traits\Regex;
use Axiom\Rivescript\Cortex\Wildcard;

/**
 * WildcardDetector class
 *
 * Description:
 *
 * This detects if there is a wildcards found in the trigger.
 *
 * Example:
 *
 * + my name is *
 * - Pleased to meet you, <star>.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Trigger-Wildcards
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\Trigger\Detectors
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class WildcardDetector implements TriggerDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger has a wildcard.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return void
     */
    #[TriggerDetector]
    public function detect(TriggerCommand $trigger): void
    {

        $wildcards = [];
        $org = $value = $trigger->getNode()->getValue();
        $parsed = [];

        $types = Wildcard::getAvailableTyppes();

        foreach ($types as $key => $type) {
            $types[$key]['number'] = 0;
        }

        foreach ($types as $character => $info) {
            $lastPos = 0;
            $positions = [];

            while (strpos($value, $character, $lastPos) > -1) {
                if ($lastPos > strlen($value)) {
                    break;
                }

                $pos = strpos($value, $character, $lastPos);
                $positions[] = $pos;

                $lastPos = $pos + strlen($character);
            }

            /**
             * Let's sort the highest numbers first,
             * so we start replacing from end end of the string
             * to the front.
             */
            rsort($positions);

            foreach ($positions as $pos) {
                $types[$character] = $info;

                $info['number']++;
                $parsed[$pos] = new Wildcard(character: $character, type: $info['type'], stringPosition: $pos);

                $replace = "<replaced{$pos}>";
                $org = substr_replace($org, $replace, $pos, strlen($character));
            }
        }

        $value = $org;
        $detected = 0;

        if (count($parsed) > 0) {

            usort($parsed, fn(Wildcard $current, Wildcard $previous) => $current->getStringPosition() < $previous->getStringPosition() ? -1 : 1);

            /**
             * Change the <replaced0> with wildcard0.
             */
            foreach ($parsed as $order => $wildcard) {
                $pos = $wildcard->getStringPosition();
                $tag = ':' . $wildcard->getType() . $detected;

                $value = str_replace("<replaced{$pos}>", $tag, $value);
                $wildcard->setTag($tag);
                $wildcard->setOrder($order);

                $wildcards[] = $wildcard;
                $detected++;
            }
        }

        if (count($wildcards) > 0) {

            $trigger->setWildcards($wildcards);

            synapse()
                ->rivescript
                ->verbose("Trigger) Detected wildcard in :value", [
                    'value' => $trigger->getNode()->getValue()
                ]);
        }
    }
}
