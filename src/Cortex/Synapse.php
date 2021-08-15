<?php

/**
 * Synapse is where all main ingredients of Rivescript are stored.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Cortex
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Collections\Collection;

/**
 * The Synapse class.
 *
 * @property \Axiom\Collections\Collection $commands
 * @property \Axiom\Collections\Collection $triggers
 * @property \Axiom\Collections\Collection $tags
 * @property \Axiom\Collections\Collection $responses
 * @property \Axiom\Collections\Collection $conditions
 * @property Memory                        $memory
 * @property Brain                         $brain
 * @property Input                         $input
 */
class Synapse
{
    /**
     * Object hash map.
     *
     * @var array[]
     */
    private $map = [];

    /**
     * Static instance object.
     *
     * @var Synapse
     */
    public static $instance;

    /**
     * Construct a new Synapse instance.
     */
    public function __construct()
    {
        self::$instance = $this;
    }

    /**
     * Get the Synapse instance object.
     *
     * @return Synapse
     */
    public static function getInstance(): Synapse
    {
        return self::$instance;
    }

    /**
     * Magic __set method.
     *
     * @param  string  $key    The key to use to store $value.
     * @param  mixed   $value  The value to store.
     *
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->map[$key] = $value;
    }

    /**
     * Magic __get method.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->map[$key];
    }
}
