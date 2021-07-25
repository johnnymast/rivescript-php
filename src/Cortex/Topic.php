<?php

namespace Axiom\Rivescript\Cortex;

use Axiom\Collections\Collection;

class Topic
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    public $triggers;

    /**
     * @var
     */
    public $responses;


    /**
     * Create a new Branch instance.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name     = $name;
        $this->triggers = new Collection([]);
        $this->responses = new Collection([]);
    }

    /**
     * Return triggers associated with this branch.
     *
     * @return array
     */
    public function triggers()
    {
        return $this->triggers;
    }

    /**
     * Return the responses associated with this branch.
     *
     * @return array
     */
    public function responses() {
        return $this->responses;
    }

    /**
     * @param $triggers
     */
    public function setTriggers($triggers)
    {
        $this->triggers = $triggers;
    }
}
