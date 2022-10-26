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

use Axiom\Rivescript\Cortex\Attributes\AutoWire;
use Axiom\Rivescript\Cortex\Commands\Label\BeginLabel;
use Axiom\Rivescript\Cortex\Commands\Label\ObjectLabel as ObjectAlias;
use Axiom\Rivescript\Cortex\Commands\Label\TopicLabel;
use Axiom\Rivescript\Cortex\RegExpressions;

/**
 * LabelCommand class
 *
 * Description:
 *
 * This handle and validate the command type "comment".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#LABEL
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
class LabelCommand extends Command
{
    /**
     * Check the syntax for labels.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {
        if ($this->node->getTag() === '>') {
            # > LabelCommand
            #   - The "begin" label must have only one argument ("begin")
            #   - "topic" labels must be lowercase but can inherit other topics ([A-Za-z0-9_\s])
            #   - "object" labels follow the same rules as "topic" labels, but don't need be lowercase
            if ($this->matchesPattern(RegExpressions::LABEL_BEGIN_SYNTAX1, $this->node->getValue()) === true
                && $this->matchesPattern(RegExpressions::LABEL_BEGIN_SYNTAX2, $this->node->getValue()) === false) {
                $this->addSyntaxError(
                    "The 'begin' label takes no additional arguments, should be verbatim '> begin'"
                );
            } elseif ($this->matchesPattern(RegExpressions::LABEL_TOPIC_SYNTAX1, $this->node->getValue()) === true
                && $this->matchesPattern(RegExpressions::LABEL_TOPIC_SYNTAX2, $this->node->getValue()) === true) {
                $this->addSyntaxError(
                    "Topics should be lowercase and contain only numbers and letters!"
                );
            } elseif ($this->matchesPattern(RegExpressions::LABEL_OBJECT_SYNTAX1, $this->node->getValue()) === true
                && $this->matchesPattern(RegExpressions::LABEL_OBJECT_SYNTAX2, $this->node->getValue()) === true) {
                $this->addSyntaxError(
                    "Objects can only contain numbers and lowercase letters!"
                );
            }
        }

        return $this->isSyntaxValid();
    }

    /**
     * Parse the label.
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function detect(): bool
    {
        $this->execute(
            attribute: AutoWire::class,
            arguments: [$this->getNode()],
            classes: [
                BeginLabel::class,
                ObjectAlias::class,
                TopicLabel::class
            ]
        );
        return false;
    }
}