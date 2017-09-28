<?php

namespace Vulcan\Rivescript\Contracts;

use Vulcan\Rivescript\Cortex\Input;

interface Tag
{
    /**
     * Parse the response.
     *
     * @param string $source
     * @param Input $input
     *
     * @return array
     */
    public function parse($source, Input $input);
}
