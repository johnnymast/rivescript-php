<?php

namespace Vulcan\Rivescript\Cortex;

class Synapse
{
    /**
     * Object hash map.
     *
     * @var Array
     */
    private $map = array();

    /**
     * Static instance object.
     *
     * @var Object
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
     * @return Object
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Magic __set method.
     *
     * @return Void
     */
    public function __set($key, $value)
    {
        $this->map[$key] = $value;
    }

    /**
     * Magic __get method.
     *
     * @param  String  $key
     * @return Mixed
     */
    public function __get($key)
    {
        return $this->map[$key];
    }
}
