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

use Axiom\Rivescript\Cortex\Attributes\AutoInjectMemory;
use Axiom\Rivescript\Cortex\Commands\Definition\Arrays;
use Axiom\Rivescript\Cortex\Commands\Definition\Globals;
use Axiom\Rivescript\Cortex\Commands\Definition\Local;
use Axiom\Rivescript\Cortex\Commands\Definition\Person;
use Axiom\Rivescript\Cortex\Commands\Definition\Sub;
use Axiom\Rivescript\Cortex\Commands\Definition\Version;
use Axiom\Rivescript\Cortex\Commands\Definition\Variable;
use Axiom\Rivescript\Cortex\RegExpressions;

/**
 * DefinitionCommand class
 *
 * Description:
 *
 * This handle and validate the command type "definition".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#DEFINITION
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
class DefinitionCmd extends Command
{
    /**
     * Check the syntax for definitions.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {
        if ($this->getNode()->getTag() == '!') {
            $source = $this->getNode()->getSource();

            # ! DefinitionCommand
            #   - Must be formatted like this:
            #     ! type name = value
            #     OR
            #     ! type = value
            #   - Type options are NOT enforceable, for future compatibility; if RiveScript
            #     encounters a new type that it can't handle, it can safely warn and skip it.
            if ($this->matchesPattern(RegExpressions::DEFINITION_SYNTAX1, $source) === false) {
                $this->addSyntaxError(
                    "Invalid format for !DefinitionCommand line: must be '! type name = value' OR '! type = value'"
                );
            }
        }

        return $this->isSyntaxValid();
    }

    /**
     * Parse the definitons.
     *
     * @throws \ReflectionException
     * @return bool
     */
    public function detect(): bool
    {

        $this->execute(
            attribute: AutoInjectMemory::class,
            arguments: [$this->getNode()],
            classes: [
                Arrays::class,
                Globals::class,
                Person::class,
                Variable::class,
                Version::class,
                Local::class,
                Sub::class,
            ]
        );
        return false;
    }
}
