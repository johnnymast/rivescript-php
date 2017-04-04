<?php

namespace Vulcan\Rivescript\Cortex;

use Vulcan\Collections\Collection;

class Topic
{
    /**
     * @var String
     */
    protected $name;

    /**
     * @var Array
     */
    public $triggers;

    /**
     * Create a new Branch instance.
     *
     * @param  String  $name
     */
    public function __construct($name)
    {
        $this->name     = $name;
        $this->triggers = new Collection([]);
    }

    /**
     * Return triggers associated with this branch.
     *
     * @return Array
     */
    public function triggers()
    {
        return $this->triggers;
    }

    public function setTriggers($triggers)
    {
        $this->triggers = $triggers;
    }
}
