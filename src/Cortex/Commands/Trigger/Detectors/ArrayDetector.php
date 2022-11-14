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
 * ArrayDetector class
 *
 * Description:
 *
 * This detects if there are arrays found in the trigger.
 *
 * Example:
 *
 * + i am wearing a (@colors) shirt
 * - I don't know if I have a shirt that's colored <star>.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Arrays-in-Triggers
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
class ArrayDetector implements TriggerDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger contains an array.
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
        if ($this->matchesPattern(RegExpressions::TRIGGER_DETECT_ARRAY, $value) === true) {
            $matches = $this->getMatchesFromPattern(RegExpressions::TRIGGER_DETECT_ARRAY, $value);
            $detected = 0;

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches as $match) {
                    if (isset($match[1])) {
                        $original = '('.$match[0].')';
                        $name = $match[1];

                        if (synapse()->memory->arrays()->has($name)) {
                            $arrays = $trigger->getArrays();
                            $arrays[] = $name;

                            $trigger->setArrays($arrays);

                            $value = str_replace($original, ':array'.$detected, $value);
                            $detected++;
                        }
                    }
                }

                $trigger->getNode()->setContent($value);
            }

            synapse()
                ->rivescript
                ->verbose("Trigger) Detected array(s) in :value", [
                    'value' => $trigger->getNode()->getValue()
                ]);
        }
    }
}
