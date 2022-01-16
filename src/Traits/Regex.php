<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Traits;

/**
 * Str trait
 *
 * A collection of regex helpers.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Traits
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
trait Regex
{

    /**
     * Does the source have any matches?
     *
     * @param string $pattern The pattern to match.
     * @param string $source  The source to match in.
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
     * @param string $pattern The pattern to match.
     * @param string $source  The source to match in.
     *
     * @return array[]|bool
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
