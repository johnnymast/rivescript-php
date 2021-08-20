<?php

/**
 * The logic for handling Topics.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Cortex
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Collections\Collection;

/**
 * The Topic class.
 */
class Topic
{
    /**
     * The name of this Topic.
     *
     * @var string
     */
    protected $name;

    /**
     * The triggers for this Topic.
     *
     * @var Collection<string, mixed>
     */
    public $triggers;

    /**
     * The responses for this Topic.
     *
     * @var Collection<string, mixed>
     */
    public $responses;

    /**
     * Create a new Topic instance.
     *
     * @param  string  $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->triggers = new Collection([]);
        $this->responses = new Collection([]);
    }

    /**
     * Return triggers associated with this branch.
     *
     * @return Collection<string, mixed>
     */
    public function triggers(): Collection
    {
        return $this->triggers;
    }

    /**
     * Return the responses associated with this branch.
     *
     * @return Collection<string, mixed>
     */
    public function responses(): Collection
    {
        return $this->responses;
    }
}
