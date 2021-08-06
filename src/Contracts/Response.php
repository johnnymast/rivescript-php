<?php


namespace Axiom\Rivescript\Contracts;

interface Response
{
    /**
     * Parse the response.
     *
     * @return bool
     */
    public function parse();
}
