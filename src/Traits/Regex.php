<?php

/**
 * The base class of Responses.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Traits
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Traits;

/**
 * Trait Regex
 * @package Axiom\Rivescript\Traits
 */
trait Regex
{

    /**
     * Does the source have any matches?
     *
     * @param  string  $pattern  The pattern to match.
     * @param  string  $source   The source to match in.
     *
     * @return bool
     */
    protected function matchesPattern(string $pattern, string $source): bool
    {
        preg_match_all($pattern, $source, $matches);

        return isset($matches[0][0]);
    }

    /**
     * Get the regular expression matches from the source.
     *
     * @param  string  $pattern  The pattern to match.
     * @param  string  $source   The source to match in.
     *
     * @return array|bool
     */
    protected function getMatchesFromPattern(string $pattern, string $source)
    {
        if ($this->matchesPattern($pattern, $source)) {
            preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);

            return $matches;
        }

        return false;
    }
}