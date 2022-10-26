<?php

namespace Axiom\Rivescript\Cortex\Attributes;

/**
 * TriggerDetector Attribute class
 *
 * Description:
 *
 * This attribute is used to auto-detect variables/wildcards etc from the input string.
 *
 * @see \Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\WildcardDetector::detect()
 * @see \Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\ArrayDetector::detect()
 * @see \Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\OptionalDetector::detect()
 * @see \Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\WeightedDetector::detect()
 * @see \Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\WildcardDetector::detect()
 *
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Attributes
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
#[\Attribute] #[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_METHOD)]
class TriggerDetector
{
    //
}