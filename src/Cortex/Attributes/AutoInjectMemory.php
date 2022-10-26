<?php

namespace Axiom\Rivescript\Cortex\Attributes;

use \Attribute;
use Axiom\Collections\Collection;

/**
 * AutoInjectMemory Attribute class
 *
 * Description:
 *
 * This attribute will inject values into memory.
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
class AutoInjectMemory
{

    public function __construct(
        protected string $name = '',
    )
    {
    }

    /**
     * Return the storage collection for this
     * memory type.
     *
     * @return \Axiom\Collections\Collection
     */
    public function getStorage(): Collection
    {
        return call_user_func(callback: [synapse()->memory, $this->name]);
    }
}