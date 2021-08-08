<?php

/**
 * Definition of the Condition contract.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Contracts
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Contracts;

interface Condition
{
    /**
     * Parse condition.
     *
     * @param  string  $source
     *
     * @return false|string
     */
    public function parse(string $source);
}
