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
 * AlternationDetector class
 *
 * Description:
 *
 * This detects if there are alternations found in the trigger.
 *
 * Example:
 *
 * + are you (okay|alright)
 * - yes i am
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Trigger-Alternations
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
class AlternationDetector implements TriggerDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger contains an alternation.
     * False will be returned if not detected.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return string
     */
    #[TriggerDetector]
    public function detect(TriggerCommand $trigger): void
    {
        $value = $trigger->getNode()->getValue();

        // FIXME: If array has aa space on it like ( @color ) it will be detected aas a alternation.

        if ($this->matchesPattern(RegExpressions::TRIGGER_DETECT_ALTERNATION, $value) === true) {
            $matches = $this->getMatchesFromPattern(RegExpressions::TRIGGER_DETECT_ALTERNATION, $value);
            $alternations = [];
            $detected = 0;

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches as $set) {
                    if (isset($set[2]) === true) {
                        $original = $set[0];
                        $extracted = $set[2];

                        if (strpos($extracted, '|') > -1) {
                            $extracted = explode('|', $extracted);
                        } else {
                            $extracted = [$extracted];
                        }

                        $alternations[] = array_map(fn($value) => trim($value), $extracted);
                        $value = str_replace($original, ':alternation'.$detected, $value);
                        $detected++;
                    }
                }

                $trigger->getNode()->setValue($value);
                $trigger->setAlternations($alternations);

                synapse()
                    ->rivescript
                    ->verbose("Trigger) Detected alternation(s) in :value", [
                        'value' => $trigger->getNode()->getValue()
                    ]);
            }
        }
    }
}
