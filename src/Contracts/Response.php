<?php


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
