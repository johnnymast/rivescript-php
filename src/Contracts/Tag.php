<?php

/**
 * Definition of the Tag contract.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Contracts
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Contracts;

use Axiom\Rivescript\Cortex\Input;

interface Tag
{
    /**
     * Parse the response.
     *
     * @param  string  $source  The string containing the Tag.
     * @param  Input   $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, Input $input): string;
}
