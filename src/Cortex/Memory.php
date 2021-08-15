<?php

/**
 * A node contains a line from rivescript files.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Cortex
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\MiniStack\MiniStack;

/**
 * The memory class.
 */
class Memory
{
    /**
     * A collection of person variables.
     *
     * @var Collection<string, mixed>
     */
    protected $person;

    /**
     * A collection of short term memory items.
     *
     * @var Collection<string, mixed>
     */
    protected $shortTerm;

    /**
     * A collection of substitutes.
     *
     * @var Collection<string, mixed>
     */
    protected $substitute;

    /**
     * A collection of variables.
     *
     * @var Collection<string, mixed>
     */
    protected $variables;

    /**
     * A collection of user variables.
     *
     * @var Collection<string, mixed>
     */
    protected $user;

    /**
     * @var Collection<string, mixed>
     */
    protected $global;

    /**
     * @var Collection<string, mixed>
     */
    private $arrays;

    /**
     * A Collection of the latest Input's
     *
     * @var MiniStack<int, string>
     */
    protected $inputs;

    /**
     * A Collection of the latest replies.
     *
     * @var MiniStack<int, string>
     */
    protected $replies;

    /**
     * Create a new Memory instance.
     */
    public function __construct()
    {
        $this->shortTerm = Collection::make([]);
        $this->substitute = Collection::make([]);
        $this->variables = Collection::make([]);
        $this->global = Collection::make([]);
        $this->arrays = Collection::make([]);
        $this->person = Collection::make([]);
        $this->user = Collection::make([]);
        $this->inputs = new MiniStack(9);
        $this->replies = new MiniStack(9);
    }

    /**
     * Stored global variables.
     *
     * @return Collection<string, mixed>
     */
    public function global(): Collection
    {
        return $this->global;
    }

    /**
     * Stored person variables.
     *
     * @return Collection<string, mixed>
     */
    public function person(): Collection
    {
        return $this->person;
    }

    /**
     * Short-term memory cache.
     *
     * @return Collection<string, mixed>
     */
    public function shortTerm(): Collection
    {
        return $this->shortTerm;
    }

    /**
     * Stored substitute variables.
     *
     * @return Collection<string, mixed>
     */
    public function substitute(): Collection
    {
        return $this->substitute;
    }

    /**
     * Stored variables.
     *
     * @return Collection<string, mixed>
     */
    public function variables(): Collection
    {
        return $this->variables;
    }

    /**
     * Return the latest 9 inputs.
     *
     * @return MiniStack<int, string>
     */
    public function inputs(): MiniStack
    {
        return $this->inputs;
    }

    /**
     * Return the latest 9 replies.
     *
     * @return MiniStack<int, string>
     */
    public function replies(): MiniStack
    {
        return $this->replies;
    }

    /**
     * Stored arrays.
     *
     * @return Collection<string, mixed>
     */
    public function arrays(): Collection
    {
        return $this->arrays;
    }

    /**
     * Stored user data.
     *
     * @param  string  $user  The user to store information for.
     *
     * @return Collection<string, mixed>
     */
    public function user(string $user = 'local-user'): Collection
    {
        if (!$this->user->has($user)) {
            $data = new Collection([]);

            $this->user->put($user, $data);
        }

        return $this->user->get($user);
    }
}
