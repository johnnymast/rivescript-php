<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands\Label;

use Axiom\Rivescript\Cortex\Attributes\AutoWire;
use Axiom\Rivescript\Cortex\Node;

/**
 * Object class
 *
 * This class handles the "> object" label type.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands\LabelCommand
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ObjectLabel
{
    /**
     * This is the variable prefix after
     * the bang (!) symbol.
     *
     * @var string
     */
    protected string $type = 'object';

    /**
     * Parse object label.
     *
     * @param \Axiom\Rivescript\Cortex\Node $node A Line from the script.
     *
     * @return void
     */
    #[AutoWire]
    public function parse(Node $node): void
    {
        if (str_starts_with($node->getValue(), $this->type)) {
            $value = trim(substr($node->getValue(), strlen($this->type)));
            $data = explode(' ', $value);

            if (count($data) === 2) {
                [$name, $language] = $data;
                $code = $node->getCommand()->getContent();

                synapse()->brain->createCodeObject($name, $language, $code);
            }
        }
    }
}