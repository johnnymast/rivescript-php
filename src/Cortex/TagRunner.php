<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\Tags\TagInterface;

/**
 * TagRunner class
 *
 * Description:
 *
 * The tag runner addes the logic of the tags to
 * the triggers and responses.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class TagRunner
{
    /**
     * @param string                        $respponseType
     * @param \Axiom\Rivescript\Cortex\Node $node
     *
     * @return void
     */
    public static function run(string $respponseType, Command $command): void
    {
        foreach (synapse()->tags as $tagClass) {
            $instance = new $tagClass($respponseType);

            if ($instance instanceof TagInterface) {
                if ($instance->sourceAllowed() && $instance->isMatching($command->getNode())) {
                    $instance->parse($command);
                }
            }
        }
    }
}