<?php

namespace Axiom\Rivescript\Cortex\Attributes;

use \Attribute;
use Axiom\Collections\Collection;

/**
 * Detector Attribute class
 *
 * Description:
 *
 * This attribute is used to auto-detect variables/wildcards etc from the input string.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#Trigger-Optionals
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
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_METHOD)]
class AutoWire
{
    //
}