<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\Commands\CommentCommand;
use Axiom\Rivescript\Cortex\Commands\ConditionCommand;
use Axiom\Rivescript\Cortex\Commands\ContinueCmd;
use Axiom\Rivescript\Cortex\Commands\ContinueCommand;
use Axiom\Rivescript\Cortex\Commands\DefinitionCommand;
use Axiom\Rivescript\Cortex\Commands\LabelCommand;
use Axiom\Rivescript\Cortex\Commands\PreviousCommand;
use Axiom\Rivescript\Cortex\Commands\RedirectCommand;
use Axiom\Rivescript\Cortex\Commands\ResponseAbstract;
use Axiom\Rivescript\Cortex\Commands\ResponseCmd;
use Axiom\Rivescript\Cortex\Commands\UnknownCommand;
use Axiom\Rivescript\Cortex\Commands\TriggerCommand;
use Axiom\Rivescript\Cortex\Traits\Regex;
use Axiom\Rivescript\Utils\Str;

/**
 * Node class
 *
 * The Node class stores information about a parsed
 * line from the script.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Node
{
    use Regex;

    /**
     * The string on the line.
     *
     * @var string
     */
    protected string $original = '';

    /**
     * The string on the line.
     *
     * @var string
     */
    protected string $source = '';

    /**
     * The command string.
     *
     * @var string
     */
    protected string $value = '';

    /**
     * Alterations will be done on
     * this string.
     *
     * @var string
     */
    protected string $content = '';

    /**
     * The line number of the line.
     *
     * @var int
     */
    protected int $lineNumber = 0;

    /**
     * This will be the command tag +,-,^ etc.
     *
     * @var string
     */
    protected string $tag = '';

    /**
     * The class of the command type.
     *
     * @var ?Command
     */
    protected ?Command $command = null;

    /**
     * Create a new Source instance.
     *
     * @param string $source The source line.
     * @param int    $number The line number in the script.
     */
    public function __construct(string $source, int $number)
    {
        $this->original = $source;
        $this->source = Str::removeWhitespace($this->original);

        $this->lineNumber = $number;

        $this->command = $this->detectCommand();
    }


    /**
     * @return void
     */
    public function reset(): void
    {
        $this->content = $this->original;
        $this->source = Str::removeWhitespace($this->original);
    }

    /**
     * Return the line string
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Return resulting command string
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * This will be the command symbol.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Return the original line source. This
     * may still include whitespace symbols.
     *
     * @return string
     */
    public function getOriginalSource(): string
    {
        return $this->original;
    }

    /**
     * Return the line number of this line.
     *
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * Return the command for this line.
     *
     * @return \Axiom\Rivescript\Cortex\Commands\Command
     */
    public function getCommand(): Command
    {
        return $this->command;
    }

    /**
     * Detect the type of command.
     *
     * @return Command
     */
    private function detectCommand(): Command
    {
        $this->tag = current(explode(" ", $this->source));

        $class = match ($this->tag) {
            '!' => DefinitionCommand::class,
            '>' => LabelCommand::class,
            '+' => TriggerCommand::class,
            '-' => ResponseCmd::class,
            '^' => ContinueCmd::class,
            '%' => PreviousCommand::class,
            '@' => RedirectCommand::class,
            '*' => ConditionCommand::class,
            "//", '#' => CommentCommand::class,
            default => UnknownCommand::class,
        };

        $this->value = $this->content = trim(mb_substr($this->source, 1));

        return new $class($this);;
    }


    /**
     * Update the content.
     *
     * @param string $content The value to set.
     *
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Update the value.
     *
     * @param string $value The value to set.
     *
     * @return void
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
