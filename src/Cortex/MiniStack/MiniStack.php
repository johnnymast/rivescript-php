<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\MiniStack;

use Axiom\Collections\Collection;

/**
 * MiniStack class
 *
 * The mini stack remembers an x number of items on
 * its stack. This value is passed through to the constructor.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\MiniStack
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class MiniStack extends Collection
{
    /**
     * The stack size.
     *
     * @var int
     */
    protected int $size = 0;

    /**
     * MiniStack Constructor.
     *
     * @param int $size The maximum amount of slices the stack can contain.
     */
    public function __construct(int $size = 0)
    {
        parent::__construct();

        $this->size = $size;
    }

    /**
     * Push an item to the MiniStack.
     *
     * @param mixed $value The value to push
     *
     * @return void
     */
    public function push($value): void
    {
        if ($this->count() >= $this->size) {
            $keys = array_keys($this->all());
            $this->remove($keys[0]);
        }

        parent::push($value);
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
