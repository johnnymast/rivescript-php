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
 * CommentCommand class
 *
 * Description:
 *
 * This handle and validate the command type "comment".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#COMMENT
 *
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
class CommentCommand extends Command
{
    /**
     * This function will return true if the syntax
     * is correct. If the syntax is not correct it
     * will return false.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {

        if ($this->getNode()->getTag() == '#') {
            $this->addSyntaxError(
                "Using the # symbol for comments is deprecated. Found on line :line",
                [
                    'line' => $this->node->getLineNumber(),
                ]
            );
        }


        return $this->isSyntaxValid();
    }

    /**
     * Parse the comment. In this case we just return true
     * because there is no information to gain from them.
     *
     * @return bool
     */
    public function detect(): bool
    {
        return true;
    }
}
