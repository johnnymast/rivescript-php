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

/**
 * Synapse class
 *
 * The Synapse is used in the brain as a storage
 * container for all kinds of information.
 *
 * PHP version 7.4 and higher.
 *
 * @property \Axiom\Collections\Collection $commands
 * @property \Axiom\Collections\Collection $triggers
 * @property \Axiom\Collections\Collection $tags
 * @property \Axiom\Collections\Collection $responses
 * @property \Axiom\Collections\Collection $conditions
 * @property \Axiom\Rivescript\Rivescript  $rivescript
 * @property Memory                        $memory
 * @property Brain                         $brain
 * @property Input                         $input
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Synapse
{
    /**
     * Object hash map.
     *
     * @var array[]
     */
    private array $map = [];

    /**
     * Static instance object.
     *
     * @var Synapse
     */
    public static Synapse $instance;

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
     * @param string $key   The key to use to store $value.
     * @param mixed  $value The value to store.
     *
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->map[$key] = $value;
    }

    /**
     * Check if a key has been set.
     *
     * @param string $key The key to use to store a value.
     *
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return (isset($this->map[$key]) === true);
    }

    /**
     * Magic __get method.
     *
     * @param string $key The key to use to store a value.
     *
     * @return array
     */
    public function __get(string $key)
    {
        return $this->map[$key];
    }
}
