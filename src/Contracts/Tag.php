<?php

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
     * @return array
     */
    public function parse(string $source, Input $input);
}
