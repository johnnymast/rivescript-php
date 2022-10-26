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

use Axiom\Rivescript\Cortex\Attributes\TriggerDetector;
use Axiom\Rivescript\Cortex\Attributes\FindTrigger;
use Axiom\Rivescript\Cortex\Commands\Trigger\AtomicTrigger;
use Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\AlternationDetector;
use Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\ArrayDetector;
use Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\OptionalDetector;
use Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\WeightedDetector;
use Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\WildcardDetector;
use Axiom\Rivescript\Cortex\Commands\Trigger\Detectors\WildcardTrigger;
use Axiom\Rivescript\Cortex\Commands\Trigger\PreviousTrigger;
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue;

/**
 * TriggerCommand class
 *
 * Description:
 *
 * This handle and validate the command type "trigger".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#TRIGGER
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
class TriggerCommand extends Command
{

    /**
     * Store the alternations for this
     * trigger.
     *
     * @var array
     */
    protected array $alternations = [];

    /**
     * Store the priority for this
     * trigger.
     *
     * @var int
     */
    protected int $priority = 0;

    /**
     * Store the arrays in this
     * trigger.
     *
     * @var array
     */
    protected array $arrays = [];

    /**
     * Store the optionals in this
     * trigger.
     *
     * @var array
     */
    protected array $optionals = [];

    /**
     * A list of responses for this trigger.
     *
     * @var \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue
     */
    protected ResponseQueue $responses;

    /**
     * @param \Axiom\Rivescript\Cortex\Node $node
     * @param array                         $syntaxErrors
     * @param string                        $content
     */
    public function __construct(Node $node, array $syntaxErrors = [], string $content = '')
    {
        parent::__construct($node, $syntaxErrors, $content);

        $this->responses = new ResponseQueue($this);
    }

    /**
     * Check the syntax for triggers.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {

        if ($this->getNode()->getTag() === '+') {
            $utf8 = synapse()->rivescript->utf8;
            $value = $this->getNode()->getValue();
            if ($utf8 === true) {
                if ($this->matchesPattern(RegExpressions::TRIGGER_SYNTAX1, $value) === true) {
                    $this->addSyntaxError(
                        "Triggers can't contain uppercase letters, backslashes or dots in UTF-8 mode."
                    );
                }
            } elseif ($this->matchesPattern(RegExpressions::TRIGGER_SYNTAX2, $value) === true) {
                $this->addSyntaxError(
                    "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > ="
                );
            }

            $parens = 0; # Open parenthesis
            $square = 0; # Open square brackets
            $curly = 0; # Open curly brackets
            $chevron = 0; # Open angled brackets
            $len = strlen($value);

            for ($i = 0; $i < $len; $i++) {
                $chr = $value[$i];

                # Count brackets.
                if ($chr === '(') {
                    $parens++;
                }
                if ($chr === ')') {
                    $parens--;
                }
                if ($chr === '[') {
                    $square++;
                }
                if ($chr === ']') {
                    $square--;
                }
                if ($chr === '{') {
                    $curly++;
                }
                if ($chr === '}') {
                    $curly--;
                }
                if ($chr === '<') {
                    $chevron++;
                }
                if ($chr === '>') {
                    $chevron--;
                }
            }

            if ($parens) {
                $this->addSyntaxError(
                    "Unmatched " . ($parens > 0 ? "left" : "right") . " parenthesis bracket ()"
                );
            }
            if ($square) {
                $this->addSyntaxError(
                    "Unmatched " . ($square > 0 ? "left" : "right") . " square bracket []"
                );
            }
            if ($curly) {
                $this->addSyntaxError(
                    "Unmatched " . ($curly > 0 ? "left" : "right") . " curly bracket {}"
                );
            }
            if ($chevron) {
                $this->addSyntaxError(
                    "Unmatched " . ($chevron > 0 ? "left" : "right") . " angled bracket <>"
                );
            }
        }

        return $this->isSyntaxValid();
    }

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseCommand $response
     * @param string                                            $type
     *
     * @return void
     */
    public function attachResponse(ResponseCommand $response, string $type): void
    {
        $response->setTrigger($this);
        $this->responses->attach($response, $type);
    }

    /**
     * Return the trigger type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return the response queue for this trigger.
     *
     * @return \Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueue
     */
    public function getQueue(): ResponseQueue
    {
        return $this->responses;
    }

    /**
     * @throws \ReflectionException
     */
    public function detect(): bool
    {
        /**
         * Let's determine the type of
         * trigger we are working with.
         */
        $type = $this->execute(
            attribute: FindTrigger::class,
            arguments: [$this],
            classes: [
                PreviousTrigger::class,
                AtomicTrigger::class,
            ]
        ) ?? "atomic";

        /**
         * Lets run some detectors on this
         * trigger to see what it contains.
         */
        $this->execute(
            attribute: TriggerDetector::class,
            arguments: [$this],
            classes: [
                WildcardDetector::class,
                ArrayDetector::class,
                AlternationDetector::class,
                PreviousTrigger::class,
                OptionalDetector::class,
            ]
        );

        $this->setType($type);

        $value = $this->getNode()->getValue();

        synapse()->rivescript->verbose("Detected trigger type :type for trigger :value", [
            'type' => $this->type,
            'value' => $value,
        ]);

        synapse()->memory->shortTerm()->put('trigger', $this);

        /**
         * Register this trigger
         */
        $topicName = synapse()->memory->shortTerm()->get('topic') ?: 'random';
        $topic = synapse()->brain->topic($topicName);

        $topic->addTrigger($this);

        return true;
    }

    /**
     * @return bool|$this
     */
    public function parse(): TriggerCommand|bool
    {
        $input = synapse()->input;

        if ($this->isFullyAtomic()) {
            if ($input->source() == $this->getNode()->getValue()) {
                return $this;
            }
        } else {

            // FIXME: Move this somewhere
            if ($this->hasWildcards()) {
                $wildcards = $this->getWildcards();
                $detected = 0;

                foreach ($wildcards as $wildcard) {
                    $parsedTrigger = preg_replace($wildcard->getSearchRegex(), '(' . $wildcard->getReplaceRegex() . ')', $this->getNode()->getValue());

                    if ($parsedTrigger === $this->getNode()->getValue()) {
                        continue;
                    }
                    echo "Parsed trigger: {$parsedTrigger}\n";
                    if (@preg_match_all('/' . $parsedTrigger . '$/iu', synapse()->input->source(), $wildcards)) {

                        $value = $this->getNode()->getValue();
                        echo "Value: {$value}\n";


                        $detected++;
                      //  return $this;
                    }
                }

                $count = count($wildcards);
                $value = $this->getNode()->getValue();
                $source = synapse()->input->source();
                echo "Detected: {$detected} vs {$count} for {$source} - Trigger: {$value} \n";
                if ($detected == count($wildcards)-1) {
                    return $this;
                }
            }



//            echo "{$this->getNode()->getValue()} is not fully attomic\n";
        }

        return false;
    }

    /**
     * Set the arrays.
     *
     * @param array $arrays The arrays to set.
     */
    public function setArrays(array $arrays): void
    {
        $this->arrays = $arrays;
    }



    /**
     * Set the alternations.
     *
     * @param array $alternations The alternations to set.
     *
     * @return void
     */
    public function setAlternations(array $alternations): void
    {
        $this->alternations = $alternations;
    }

    /**
     * Set the optionals.
     *
     * @param array $optionals The optionals to set.
     *
     * @return void
     */
    public function setOptionals(array $optionals): void
    {
        $this->optionals = $optionals;
    }

    /**
     * Set the priority for this trigger.
     *
     * @param int $priority The priority to set.
     *
     * @return void
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Check to see if this trigger has arrays.
     * The answer is true or false.
     *
     * @return bool
     */
    public function hasArrays(): bool
    {
        return (count($this->arrays) > 0);
    }

    /**
     * Answer the question if this trigger
     * has alternations true or false.
     *
     * @return bool
     */
    public function hasAlternations(): bool
    {
        return (count($this->alternations) > 0);
    }

    /**
     * Answer the question if this trigger
     * has optionals yes or no.
     *
     * @return bool
     */
    public function hasOptionals(): bool
    {
        return (count($this->optionals) > 0);
    }

    /**
     * Answer the question if this trigger
     * has priority true or false.
     *
     * @return bool
     */
    public function hasPriority(): bool
    {
        return ($this->priority > 0);
    }

    /**
     * Check if this trigger is truly atomic
     * true or false.
     *
     * @return bool
     */
    public function isFullyAtomic(): bool
    {
        return (
            $this->hasArrays() === false &&
            $this->hasWildcards() === false &&
            $this->hasAlternations() === false &&
            $this->hasOptionals() === false &&
            $this->hasPriority() === false
        );
    }

    /**
     * Return the arrays for this trigger.
     *
     * @return array
     */
    public function getArrays(): array
    {
        return $this->arrays;
    }

    /**
     * Return the alternations for this
     * trigger.
     *
     * @return array
     */
    public function getAlternations(): array
    {
        return $this->alternations;
    }

    /**
     * Return the optionals for this
     * trigger.
     *
     * @return array
     */
    public function getOptionals(): array
    {
        return $this->optionals;
    }

    /**
     * Return the priority for this
     * trigger.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
