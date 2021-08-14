<?php

/**
 * Tags helper trait.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Traits
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Traits;

/**
 * Trait Tags
 */
trait Tags
{
    /**
     * Parse the response through the available tags.
     *
     * @param  string  $response  The response string to parse.
     *
     * @return string
     */
    protected function parseTags(string $response): string
    {
        synapse()->tags->each(
            function ($tag) use (&$response) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagClass = new $class();

                $response = $tagClass->parse($response, synapse()->input);
            }
        );

        return $response;
    }
}
