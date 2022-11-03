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

// FIXME
class ArrayDetector implements ResponseDetectorInterface
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
        $content = $response->getNode()->getContent();

        if ($this->matchesPattern(RegExpressions::RESPONSE_RANDOM_ARRAY_VALUE, $value) === true) {
            $matches = $this->getMatchesFromPattern(RegExpressions::RESPONSE_RANDOM_ARRAY_VALUE, $value);

            if (is_array($matches) && count($matches) > 0) {
                foreach ($matches as $match) {
                    $name = $match[1];
                    if (synapse()->memory->arrays()->has($name)) {
                        $array = synapse()->memory->arrays()->get($name);
                        $random = array_rand($array, 1);
                        $replacement = $array[$random];
                        $value = str_replace("(@$name)", $replacement, $value);
                        $content = str_replace("(@$name)", $replacement, $content);
                    }
                }

                $response->getNode()->setValue($value);
                $response->getNode()->setContent($content);
            }

//            synapse()
//                ->rivescript
//                ->verbose("Response) Detected array(s) in :value", [
//                    'value' => $trigger->getNode()->getValue()
//                ]);
        }

    }
}
