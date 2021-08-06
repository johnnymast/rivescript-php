<?php

/**
 * Definition of the Response contract.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Contracts
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Contracts;

interface Response
{
    /**
     * Parse the response.
     *
     * @return bool|string
     */
    public function parse();
}
