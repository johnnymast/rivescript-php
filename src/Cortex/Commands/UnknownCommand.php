<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Commands;

/**
 * UnknownCommand class
 *
 * Description:
 *
 * This dummy class represent unrecognized types of commands.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Commands
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class UnknownCommand extends Command
{
    /**
     * This function will return true because unknown
     * commands will be ignored.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {
        $this->addSyntaxError("UnknownCommand command in ':command' on line :line", [
            'command' => $this->node->getSource(),
            'line' => $this->node->getLineNumber(),
        ]);

        return $this->isSyntaxValid();
    }

    /**
     * This abstract function has to be implemented
     * by it will be ignored for commands of type
     * unknown.
     *
     * @return bool
     */
    public function detect(): bool
    {
        // This is not used for commands of type unknown.
        return false;
    }
}
