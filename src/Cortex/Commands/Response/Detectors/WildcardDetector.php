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
use Axiom\Rivescript\Cortex\Traits\Regex;
use Axiom\Rivescript\Cortex\Wildcard;

/**
 * WildcardDetector class
 *
 * Description:
 *
 * This detects if there is are wildcards found in the response. This will be important
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
class WildcardDetector implements ResponseDetectorInterface
{
    use Regex;

    /**
     * Detect if the type of trigger has wildcards.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseCommand $response
     *
     * @return void
     */
    #[ResponseDetector]
    public function detect(ResponseCommand $response): void
    {
        $value = $org = $response->getNode()->getValue();

        $wildcards = [];

        //$input = synapse()->input->source();
        $parsed = [];

        $types = Wildcard::getAvailableTyppes();

        foreach ($types as $key => $type) {
            $types[$key]['number'] = 0;
        }

//        foreach ($types as $character => $info) {
//            $lastPos = 0;
//
//            while (($pos = strpos($org, $character, $lastPos)) > -1) {
//                $types[$character] = $info;
//
//                $info['number']++;
//                $parsed[$pos] = new Wildcard(character: $character, type: $info['type'], stringPosition: $pos);
//
//                $replace = "<replaced{$pos}>";
//
//                $org = substr_replace($org, $replace, $pos, strlen($character));
//
//                $lastPos = $pos + strlen("<replaced{$pos}>");
//            }
//        }


        if ($value == "i like red") {
            echo "Response wildcards for {$value}\n";
            print_r($wildcards);
            exit;
        }
    }
}
