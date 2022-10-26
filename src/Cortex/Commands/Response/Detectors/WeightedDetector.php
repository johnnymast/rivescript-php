<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands\Response\Detectors;

use Axiom\Rivescript\Cortex\Attributes\ResponseDetector;
use Axiom\Rivescript\Cortex\Commands\ResponseCommand;
use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Cortex\Traits\Regex;

/**
 * PriorityDetector class
 *
 * Description:
 *
 * This detects if there is a weight found in the response. This will be important
 * later if the response turns out to be a random response.
 *
 * Example:
 *
 * + hello
 * - Hello there!{weight=50}
 * - Hi.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Weighted-Random-Response
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\TriggerCommand\Detectors
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class WeightedDetector implements ResponseDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger has a priority.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseCommand $response
     *
     * @return void
     */
    #[ResponseDetector]
    public function detect(ResponseCommand $response): void
    {
        $value = $response->getNode()->getValue();

        if ($this->matchesPattern(RegExpressions::RESPONSE_DETECT_WEIGHT, $value) === true) {
            $matches = $this->getMatchesFromPattern(RegExpressions::RESPONSE_DETECT_WEIGHT, $value);

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches as $set) {
                    $original = $set[0];

                    if (is_array($set) === true && isset($set[1]) === true) {
                        $value = str_replace($original, '', $value);
                        $response->setWeight($set[1]);
                    }
                }

                $response->getNode()->setValue($value);

                synapse()
                    ->rivescript
                    ->verbose("Response) detected weight in :value", [
                        'value' => $response->getNode()->getValue()
                    ]);
            }
        }
    }
}
