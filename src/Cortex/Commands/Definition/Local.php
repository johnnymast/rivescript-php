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
 * Local class
 *
 * This class handles the definition of the local variables.
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
class Local
{
    /**
     * This is the variable prefix after
     * the bang (!) symbol.
     *
     * @var string
     */
    protected string $type = 'local';

    /**
     * Parse local variables.
     *
     * @param \Axiom\Collections\Collection<string, mixed> $storage Where to store the values to.
     * @param \Axiom\Rivescript\Cortex\Node                $node    A Line from the script.
     *
     * @return void
     */
    #[AutoInjectMemory(name: 'local')]
    public function parse(Collection $storage, Node $node): void
    {
        if (str_starts_with($node->getValue(), $this->type)) {
            $value = substr($node->getValue(), strlen($this->type));
            [$key, $value] = explode('=', $value);

            $valid = [
                'concat' => [
                    'none',
                    'space',
                    'newline'
                ]
            ];

            $key = trim($key);
            $value = trim($value);

            if (isset($valid[$key]) === true) {
                $default = current($valid[$key]);

                if (in_array($value, $valid[$key]) === false) {
                    $value = $default;

                }

                $storage->put($key, $value);
            }
        }
    }
}