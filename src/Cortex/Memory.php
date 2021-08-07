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

/**
 * The memory class.
 */
class Memory
{
    /**
     * A collection of person variables.
     *
     * @var Collection
     */
    protected $person;

    /**
     * A collection of short term memory items.
     *
     * @var Collection
     */
    protected $shortTerm;

    /**
     * A collection of substitutes.
     *
     * @var Collection
     */
    protected $substitute;

    /**
     * A collection of variables.
     *
     * @var Collection
     */
    protected $variables;

    /**
     * A collection of user variables.
     *
     * @var Collection
     */
    protected $user;

    /**
     * @var Collection
     */
    protected $global;

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
    }

    /**
     * Stored global variables.
     *
     * @return Collection
     */
    public function global(): Collection
    {
        return $this->global;
    }

    /**
     * Stored person variables.
     *
     * @return Collection
     */
    public function person(): Collection
    {
        return $this->person;
    }

    /**
     * Short-term memory cache.
     *
     * @return Collection
     */
    public function shortTerm(): Collection
    {
        return $this->shortTerm;
    }

    /**
     * Stored substitute variables.
     *
     * @return Collection
     */
    public function substitute(): Collection
    {
        return $this->substitute;
    }

    /**
     * Stored variables.
     *
     * @return Collection
     */
    public function variables(): Collection
    {
        return $this->variables;
    }

    /**
     * Stored arrays.
     *
     * @return Collection
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
     * @return Collection
     */
    public function user(string $user = '0'): Collection
    {
        if (!$this->user->has($user)) {
            $data = new Collection([]);

            $this->user->put($user, $data);
        }

        return $this->user->get($user);
    }
}
