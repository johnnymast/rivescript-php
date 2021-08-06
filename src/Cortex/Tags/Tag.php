<?php

/**
 * The abstract class for all the Tags.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Contracts\Tag as TagContract;
use LogicException;

/**
 * Class Tag
 */
abstract class Tag implements TagContract
{
    use Regex;

    /**
     * @var string
     */
    protected $sourceType;

    /**
     * Create a new Tag instance.
     *
     * @param  string  $sourceType
     */
    final public function __construct(string $sourceType = 'response')
    {
        $this->sourceType = $sourceType;

        if (!isset($this->allowedSources)) {
            throw new LogicException(get_class($this).' must have an "allowedSources" property declared.');
        }
    }

    /**
     * Determine if the type of source is allowed.
     *
     * @return bool
     */
    public function sourceAllowed(): bool
    {
        return in_array($this->sourceType, $this->allowedSources);
    }

    /**
     * Does the source have any matches?
     *
     * @param  string  $source
     *
     * @return bool
     */
    protected function hasMatches(string $source): bool
    {
        return $this->matchesPattern($this->pattern, $source);
    }

    /**
     * Get the regular expression matches from the source.
     *
     * @param  string  $source
     *
     * @return array
     */
    protected function getMatches(string $source): array
    {
        return $this->getMatchesFromPattern($this->pattern, $source);
    }
}
