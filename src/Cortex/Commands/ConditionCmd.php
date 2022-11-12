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

use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Cortex\TagRunner;
use Axiom\Rivescript\Cortex\Tags\Star;
use Axiom\Rivescript\Cortex\Tags\Tag;

/**
 * ConditionCommand class
 *
 * Description:
 *
 * This handle and validate the command type "condition".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#CONDITION
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
class ConditionCmd extends ResponseAbstract implements ResponseInterface
{
    /**
     * Check the syntax for conditions.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {

        if ($this->getNode()->getTag() == '*') {
            $value = $this->getNode()->getValue();

            if ($this->matchesPattern(RegExpressions::CONDITION_SYNTAX1, $value) === false) {
                $this->addSyntaxError(
                    "Invalid format for !ConditionCommand: should be like `* value symbol value => response`"
                );
            }
        }

        return $this->isSyntaxValid();
    }

    public function validates(): bool
    {
        $conditions = [
            'equals' => [
                'regex' => RegExpressions::CONDITION_TYPE_EQUALS,
                'condition' => fn($left, $right) => $left == $right
            ],
            'not_equals' => [
                'regex' => RegExpressions::CONDITION_TYPE_NOT_EQUALS,
                'condition' => fn($left, $right) => $left != $right
            ],
            'greater_than' => [
                'regex' => RegExpressions::CONDITION_TYPE_GREATER_THAN,
                'condition' => fn($left, $right) => $left > $right
            ],
            'greater_than_or_equals' => [
                'regex' => RegExpressions::CONDITION_TYPE_GREATER_THAN_OR_EQUALS,
                'condition' => fn($left, $right) => $left >= $right
            ],
            'less_or_equals' => [
                'regex' => RegExpressions::CONDITION_TYPE_LESS_THAN_OR_EQUALS,
                'condition' => fn($left, $right) => $left <= $right
            ],
            'less_than' => [
                'regex' => RegExpressions::CONDITION_TYPE_LESS_THAN,
                'condition' => fn($left, $right) => $left < $right
            ],
        ];

        $this->getNode()->reset();

        TagRunner::run(Tag::TRIGGER, $this);

        $value = $this->getNode()->getValue();

        foreach ($conditions as $name => $condition) {
            if ($this->matchesPattern($condition['regex'], $this->getNode()->getValue())) {
                $match = $this->getMatchesFromPattern($condition['regex'], $this->getNode()->getValue());
                $match = current($match);

                $context = $match[0];
                $left = $match[1];
                $right = $match[3];

                if ($condition['condition']($left, $right)) {

                    $content = str_replace($context, '', $value);
                    $this->getNode()->setContent(trim($content));
                    return true;
                }
            }
        }

        return false;
    }
}
