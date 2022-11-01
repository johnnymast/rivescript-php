<?php

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Cortex\Traits\Regex;
use LogicException;

abstract class Tag
{
    use Regex;

    /**
     *
     */
    public const TRIGGER = "trigger";
    /**
     *
     */
    public const RESPONSE = "response";

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
     * This variable can be overwritten by
     * a tag class to indicate it needs more
     * secure parsing.
     *
     * @var bool
     */
    protected bool $secure = false;

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
     * Test if the source if of a given type.
     *
     * @param string $type Type eithert trigger or response.
     *
     * @return bool
     */
    public function isSourceOfType(string $type): bool
    {
        return ($this->sourceType == $type);
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
     * @param Node $node
     * @return bool
     */
    public function isMatching(Node $node): bool
    {
        return $this->matchesPattern($this->pattern, $node->getValue());
    }

    /**
     * @param Node $node
     * @return array|array[]|bool
     */
    public function getMatches(Node $node): array|bool
    {
        return $this->getMatchesFromPattern($this->pattern, $node->getValue());
    }

    /**
     * @param Node $node
     * @return array|array[]|bool
     */
    public function getMatchesFromContent(Node $node): array|bool
    {
        return $this->getMatchesFromPattern($this->pattern, $node->getContent());
    }

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    abstract public function parse(Command $command): void;
}