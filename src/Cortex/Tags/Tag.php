<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Contracts\Tag as TagContract;
use LogicException;

/**
 * Tag class
 *
 * The tag base class.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
abstract class Tag implements TagContract
{
    use Regex;

    /**
     * @var string
     */
    protected string $sourceType = "response";

    /**
     * Store the allowed sources.
     *
     * @var array<string>
     */
    protected array $allowedSources = [];

    /**
     * Defines the Regex structure
     * for a tag.
     *
     * @var string
     */
    protected string $pattern = "";

    /**
     * Create a new Tag instance.
     *
     * @param string $sourceType
     */
    final public function __construct(string $sourceType = "response")
    {
        $this->sourceType = $sourceType;

        if (!isset($this->allowedSources)) {
            throw new LogicException(get_class($this) . " must have an \"allowedSources\" property declared.");
        }
    }

    /**
     * Determine if the type of source is allowed.
     *
     * @return bool
     */
    public function sourceAllowed(): bool
    {
        return in_array($this->sourceType, $this->allowedSources, true);
    }

    /**
     * Does the source have any matches?
     *
     * @param string $source
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
     * @param string $source
     *
     * @return array[]
     */
    protected function getMatches(string $source): array
    {
        return $this->getMatchesFromPattern($this->pattern, $source) ?? [];
    }

    abstract public function getTagName();
}
