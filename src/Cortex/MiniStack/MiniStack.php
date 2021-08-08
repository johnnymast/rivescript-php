<?php

/**
 * The miniStack can only remember information
 * in slice sthe size
 * determines what responses are valid or not.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     MiniStack
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\MiniStack;

use Axiom\Collections\Collection;

/**
 * Class MiniStack
 */
class MiniStack extends Collection
{
    /**
     * The stack size.
     *
     * @var int
     */
    protected $size = 0;

    /**
     * MiniStack Constructor.
     *
     * @param  int  $size  The maximum amount of slices the stack can contain.
     */
    public function __construct(int $size = 0)
    {
        $this->size = $size;
    }

    /**
     * Push an item to the MiniStack.
     *
     * @param  mixed  $value  The value to push
     *
     * @return void
     */
    public function push($value)
    {
        if ($this->count() >= $this->size) {
            $keys = array_keys($this->all());
            $this->remove($keys[0]);
        }
        return parent::push($value);
    }

    /**
     * Return the MiniStack size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
