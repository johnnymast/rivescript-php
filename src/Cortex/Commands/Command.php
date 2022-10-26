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
use Axiom\Rivescript\Cortex\Attributes\AutoInjectMemory;
use Axiom\Rivescript\Cortex\Attributes\ResponseDetector;
use Axiom\Rivescript\Cortex\Attributes\TriggerDetector;
use Axiom\Rivescript\Cortex\Attributes\FindTrigger;
use Axiom\Rivescript\Cortex\Traits\Regex;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Utils\Misc;
use ReflectionClass;

/**
 * Command class
 *
 * Description:
 *
 * This class is an abstract base class for any type of classes.
 * It will store shared information like error messages regarding
 * the syntax of the command.
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
abstract class Command implements CommandValidator
{
    use Regex;

    /**
     * Set the order of this trigger.
     *
     * @var int
     */
    protected int $order = 0;

    /**
     * The type of this command.
     *
     * @var string
     */
    protected string $type = 'atomic';

    /**
     * Store the wildcards for this
     * trigger.
     *
     * @var array
     */
    protected array $wildcards = [];

    /**
     * Command Constructor
     *
     * @param \Axiom\Rivescript\Cortex\Node $node
     * @param array                         $syntaxErrors
     * @param string                        $content
     */
    public function __construct(
        /**
         * The source node
         *
         * @var Node
         */
        public Node      $node,

        /**
         * Storage container for syntax errors.
         *
         * @var array<string>
         */
        public array     $syntaxErrors = [],
    )
    {
        $this->checkSyntax();
    }

    /**
     * Child classes will have to implement this
     * method.
     *
     * @return bool
     */
    abstract public function checkSyntax(): bool;

    /**
     * Parse the command.
     *
     * @return bool
     */
    abstract public function detect(): bool;

    /**
     * Set the type of this command.
     *
     * @param string $type The type of this command.
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Set the order of this trigger.
     *
     * @param int $order The sort order for this command.
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    /**
     * Set the wildcards.
     *
     * @param array $wildcards The wildcards to set.
     */
    public function setWildcards(array $wildcards): void
    {
        $this->wildcards = $wildcards;
    }

    /**
     * Check to see if this trigger has wildcards.
     * The answer is true or false.
     *
     * @return bool
     */
    public function hasWildcards(): bool
    {
        return (count($this->wildcards) > 0);
    }

    /**
     * Return the wildcards.
     *
     * @return array
     */
    public function getWildcards(): array
    {
        return $this->wildcards;
    }

    /**
     * Return the source node.
     *
     * @return \Axiom\Rivescript\Cortex\Node
     */
    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * Add a new syntax error.
     *
     * @param string        $message The error to add.
     * @param array<string> $args    (format) extra parameters.
     *
     * @return void
     */
    public function addSyntaxError(string $message, array $args = []): void
    {
        $this->syntaxErrors[] = Misc::formatString($message, $args);
    }

    /**
     * Set the Content for this command.
     *
     * @param string $content The content.
     *
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->getNode()->setContent($content);
    }

    /**
     * If checkSyntax returns false this function can be
     * called to return the syntax error string(s).
     *
     * @return array<string>
     */
    public function getSyntaxErrors(): array
    {
        return $this->syntaxErrors;
    }

    /**
     * Return the order of this trigger.
     *
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Return the content of this command.
     *
     * @return int
     */
    public function getContent(): string {
        return $this->getNode()->getContent();
    }

    /**
     * Return the type of this command.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Execute commands.
     *
     * @param string        $attribute The attribute to find
     * @param array[]       $arguments Optional additional arguments.
     * @param array<string> $classes   The classes the attribute can be found in.
     *
     * @throws \ReflectionException
     *
     * @return string|null
     */
    protected function execute(string $attribute, array $arguments, array $classes): ?string
    {

        foreach ($classes as $class) {
            $classInstance = new $class;
            $reflection = new ReflectionClass($classInstance);

            foreach ($reflection->getMethods() as $method) {
                $attribs = $method->getAttributes($attribute);

                foreach ($attribs as $attr) {
                    $instance = $attr->newInstance();

                    if ($instance instanceof AutoInjectMemory) {
                        $storage = $instance->getStorage();
                        call_user_func_array([$classInstance, $method->getName()], array_merge([$storage], $arguments));
                    }

                    if ($instance instanceof AutoWire) {
                        call_user_func_array([$classInstance, $method->getName()], $arguments);
                    }

                    if ($instance instanceof TriggerDetector || $instance instanceof ResponseDetector) {
                        call_user_func_array([$classInstance, $method->getName()], $arguments);
                    }

                    if ($instance instanceof FindTrigger) {
                        $result = call_user_func_array([$classInstance, $method->getName()], $arguments);

                        if ($result) {
                            return $class;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check to see if this command has parse errors.
     *
     * @return bool
     */
    public function isSyntaxValid(): bool
    {
        return (count($this->syntaxErrors) === 0);
    }

    /**
     * Is this command an empty line?
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (empty($this->node->getSource()) === true);
    }

    /**
     * Is this command a comment?
     *
     * @return bool
     */
    public function isComment(): bool
    {
        return ($this instanceof CommentCommand);
    }
}
