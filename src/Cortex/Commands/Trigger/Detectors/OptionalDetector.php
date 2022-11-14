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
use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Traits\Regex;

/**
 * OptionalDetector class
 *
 * Description:
 *
 * This detects if there are optionals found in the trigger.
 *
 * Example:
 *
 * + what is your [home] phone number
 * - It is 1234567890
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Trigger-Optionals
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
class OptionalDetector implements TriggerDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger contains an optional.
     * False will be returned if not detected.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return void
     */
    #[TriggerDetector]
    public function detect(TriggerCommand $trigger): void
    {
        $value = $trigger->getNode()->getValue();
        if ($this->matchesPattern(RegExpressions::TRIGGER_DETECT_OPTIONAL, $value) === true) {
            $matches = $this->getMatchesFromPattern(RegExpressions::TRIGGER_DETECT_OPTIONAL, $value);
            $optionals = [];
            $detected = 0;

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches as $set) {
                    if (isset($set[2]) === true) {
                        $original = $set[0];
                        $extracted = $set[2];
                        $extracted = [$extracted];

                        $optionals[] = array_map(fn($value) => trim($value), $extracted);

                        $value = str_replace($original, ':optional'.$detected, $value);
                        $detected++;
                    }
                }

                $trigger->getNode()->setValue($value);
                $trigger->setOptionals($optionals);

                synapse()
                    ->rivescript
                    ->verbose("Trigger) Detected optional(s) in :value", [
                        'value' => $trigger->getNode()->getValue()
                    ]);
            }
        }
    }
}
