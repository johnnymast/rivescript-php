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
use Axiom\Rivescript\Cortex\Traits\Regex;

/**
 * PriorityDetector class
 *
 * Description:
 *
 * This detects if there is a priority found in the trigger.
 *
 * Example:
 *
 * + {weight=100}google *
 * - Searching Google... <call>google <star></call>
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Priority-Triggers
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
class PriorityDetector implements TriggerDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger has a priority.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return void
     */
    #[TriggerDetector]
    public function detect(TriggerCommand $trigger): void
    {
        $value = $trigger->getNode()->getValue();
        if ($this->matchesPattern(RegExpressions::TRIGGER_DETECT_PRIORITY, $value) === true) {
            $matches = $this->getMatchesFromPattern(RegExpressions::TRIGGER_DETECT_PRIORITY, $value);

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches as $set) {
                    $original = $set[0];

                    if (is_array($set) === true && isset($set[1]) === true) {
                        $value = str_replace($original, '', $value);
                        $trigger->setPriority($set[1]);
                    }
                }

                $trigger->getNode()->setValue($value);

                synapse()
                    ->rivescript
                    ->verbose("Trigger) Detected priority in :value", [
                        'value' => $trigger->getNode()->getValue()
                    ]);
            }
        }
    }
}
