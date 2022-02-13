<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\MiniStack\MiniStack;

/**
 * Memory class
 *
 * The memory class stores information about different
 * parts of the brain (short or long term).
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Memory
{
    /**
     * A collection of person variables.
     *
     * @var Collection<string, mixed>
     */
    protected Collection $person;

    /**
     * A collection of short term memory items.
     *
     * @var Collection<string, mixed>
     */
    protected Collection $shortTerm;

    /**
     * A collection of substitutes.
     *
     * @var Collection<string, mixed>
     */
    protected Collection $substitute;

    /**
     * A collection of variables.
     *
     * @var Collection<string, mixed>
     */
    protected Collection $variables;

    /**
     * A collection of user variables.
     *
     * @var Collection<string, mixed>
     */
    protected Collection $user;

    /**
     * @var Collection<string, mixed>
     */
    protected Collection $global;

    /**
     * @var Collection<string, mixed>
     */
    protected Collection $local;

    /**
     * @var Collection<string, mixed>
     */
    private Collection $arrays;

    /**
     * A Collection of the latest Input's
     *
     * @var MiniStack<int, string>
     */
    protected Collection $inputs;

    /**
     * A Collection of the latest replies.
     *
     * @var MiniStack<int, string>
     */
    protected Collection $replies;

    /**
     * A Collection of the Tags.
     *
     * @var Collection<string, \Axiom\Rivescript\Cortex\Tags\Tag>
     */
    protected $tags;

    /**
     * Create a new Memory instance.
     */
    public function __construct()
    {
        $this->shortTerm = Collection::make([]);
        $this->substitute = Collection::make([]);
        $this->variables = Collection::make([]);
        $this->global = Collection::make([]);
        $this->tags = Collection::make([]);

        /**
         * none -- the default, nothing is added when continuation lines are joined together.
         * space -- continuation lines are joined by a space character (\s)
         * newline -- continuation lines are joined by a line break character (\n)
         */
        $this->local = Collection::make([
            'concat' => 'none'
        ]);

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
     * Stored parser variables.
     *
     * @return Collection<string, mixed>
     */
    public function local(): Collection
    {
        return $this->local;
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
     * Stored variables.
     *
     * @return Collection<string, mixed>
     */
    public function tags(): Collection
    {
        return $this->tags;
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
     * @param string $user The user to store information for.
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
