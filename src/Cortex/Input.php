<?php

/**
 * Input contains information about the client side of the
 * request.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Cortex
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex;

/**
 * The Input class.
 */
class Input
{
    /**
     * The source string.
     *
     * @var string
     */
    protected $source;

    /**
     * The original source string.
     *
     * @var string
     */
    protected $original;

    /**
     * The user id.
     *
     * @var int
     */
    protected $user;

    /**
     * Create a new Input instance.
     *
     * @param  string  $source  The source string.
     * @param  string  $user    The user identifier.
     */
    public function __construct(string $source, string $user = '0')
    {
        $this->original = $source;
        $this->user = $user;

        $this->cleanOriginalSource();
    }

    /**
     * Return the source input.
     *
     * @return string
     */
    public function source(): string
    {
        return $this->source;
    }

    /**
     * Return the current user speaking.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * Clean the source input, so its in a state easily readable
     * by the interpreter.
     *
     * @return void
     */
    protected function cleanOriginalSource()
    {
        $patterns = synapse()->memory->substitute()->keys()->all();
        $replacements = synapse()->memory->substitute()->values()->all();

        $this->source = mb_strtolower($this->original);;
        $this->source = preg_replace($patterns, $replacements, $this->source);
        $this->source = preg_replace('/[^\pL\d\s]+/u', '', $this->source);
        $this->source = remove_whitespace($this->source);
    }
}
