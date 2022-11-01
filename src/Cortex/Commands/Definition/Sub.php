<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands\Definition;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Attributes\AutoInjectMemory;
use Axiom\Rivescript\Cortex\Node;

/**
 * Sub class
 *
 * This class handles the definition of substitutions.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\DefinitionCommand
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Sub
{
    /**
     * This is the variable prefix after
     * the bang (!) symbol.
     *
     * @var string
     */
    protected string $type = 'sub';

    /**
     * Parse substitutions.
     *
     * @param \Axiom\Collections\Collection<string, mixed> $storage Where to store the values to.
     * @param \Axiom\Rivescript\Cortex\Node                $node    A Line from the script.
     *
     * @return void
     */
    #[AutoInjectMemory(name: 'substitute')]
    public function parse(Collection $storage, Node $node): void
    {
        if (str_starts_with($node->getValue(), $this->type)) {
            $value = substr($node->getValue(), strlen($this->type));
            [$key, $value] = explode('=', $value);

            $key = trim($key);
            $key = '/\b' . preg_quote($key, '/') . '\b/'; // Convert the "key" to a regular expression ready format
            $value = trim($value);

            $storage->put($key, $value);
        }
    }
}