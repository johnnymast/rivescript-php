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

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Attributes\FindResponse;
use Axiom\Rivescript\Cortex\Attributes\ResponseDetector;
use Axiom\Rivescript\Cortex\Commands\Response\AtomicResponse;
use Axiom\Rivescript\Cortex\Commands\Response\Detectors\ArrayDetector;
use Axiom\Rivescript\Cortex\Commands\Response\Detectors\WeightedDetector;
use Axiom\Rivescript\Cortex\Commands\Response\Detectors\WildcardDetector;

/**
 * TriggerCommand class
 *
 * Description:
 *
 * This handle and validate the command type "response".
 *
 * @see      https://www.rivescript.com/wd/RiveScript#RESPONSE
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
class ResponseCommand extends Command
{

    /**
     * Store the weight of this trigger.
     *
     * @var int
     */
    protected int $weight = 0;

    /**
     * @var \Axiom\Rivescript\Cortex\Commands\TriggerCommand
     */
    protected TriggerCommand $trigger;

    /**
     * @var array
     */
    protected array $stars = [
        ['star', 'star1'],
        'star2',
        'star3',
        'star4',
        'star5',
        'star6',
        'star7',
        'star8',
        'star9'
    ];

    /**
     * At this point in time continue for
     * rivescript-cli 2.0 response does not have
     * a syntax check. This is because tags are
     * not allowed in responses.
     *
     * @return bool
     */
    public function checkSyntax(): bool
    {
        return $this->isSyntaxValid();
    }

    /**
     * Detect the response type.
     *
     * @throws \ReflectionException
     */
    public function detect(): bool
    {
        $trigger = synapse()->memory->shortTerm()->get('trigger');

        if (!$trigger) {
            return false;
        }

        /**
         * Let's determine the type of
         * trigger we are working with.
         */
        $type = $this->execute(
            attribute: FindResponse::class,
            arguments: [$this],
            classes: [
                AtomicResponse::class,
            ]
        ) ?? "atomic";

        /**
         * @var \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
         */
        $trigger->attachResponse($this, $this->type);


        /**
         * Lets run some detectors on this
         * trigger to see what it contains.
         */
        $this->execute(
            attribute: ResponseDetector::class,
            arguments: [$this],
            classes: [
                WeightedDetector::class,
                ArrayDetector::class,
            ]
        );

        $this->setType($type);

        $value = $this->getNode()->getValue();
        $triggerText = $trigger->getNode()->getValue();

        synapse()->rivescript->verbose("Detected response type :type for \":value\" belonging to trigger \":trigger\"", [
            'type' => $this->type,
            'value' => $value,
            'trigger' => $triggerText,
        ]);


        return false;
    }

    /**
     * Set a reference to the trigger for the command.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\TriggerCommand $trigger
     *
     * @return void
     */
    public function setTrigger(TriggerCommand $trigger): void
    {
        $this->trigger = $trigger;
    }

    /**
     * Set the weight of this response. This will
     * be used in random responses.
     *
     * @param int $weight The weight to set.
     *
     * @return void
     */
    public function setWeight(int $weight = 0): void
    {
        $this->weight = $weight;
    }

    /**
     * Check to see if this response has a weight.
     * The answer is true or false.
     *
     * @return bool
     */
    public function hasWeight(): bool
    {
        return ($this->weight > 0);
    }

    /**
     * Return the trigger this response belongs to.
     *
     * @return \Axiom\Rivescript\Cortex\Commands\TriggerCommand
     */
    public function getTrigger(): TriggerCommand
    {
        return $this->trigger;
    }

    /**
     * Return the weight for this command.
     *
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }


    /**
     * @return void
     */
    public function invokeStars()
    {

        //if (!$this->getTrigger()->hasStars()) {

        $this->getTrigger()->stars = new Collection([]);

            $wildcards = [
                '/_/' => '[^\s\d]+?',
                '/#/' => '\\d+?',
                '/\*/' => '.*?',
                '/<zerowidthstar>/' => '^\*$',
            ];

            $trigger = $this->getTrigger();
            $value = $this->getTrigger()
                ->getNode()
                ->getValue();

            foreach ($wildcards as $pattern => $replacement) {
                $parsedTrigger = preg_replace($pattern, '(' . $replacement . ')', $value);

                if ($parsedTrigger === $value) {
                    continue;
                }

                if (@preg_match_all('/' . $parsedTrigger . '$/iu', synapse()->input->source(), $parsed)) {
                    array_shift($parsed);

                    if (is_array($parsed)) {
                        foreach ($this->stars as $index => $star) {
                            if (isset($parsed[$index])) {
                                if ($index == 0 && is_array($star) === true) {
                                    foreach ($star as $name) {
                                        $trigger->stars->put('<' . $name . '>', current($parsed[$index]));
                                    }
                                } else {
                                    $trigger->stars->put('<' . $star . '>', current($parsed[$index]));
                                }
                            }
                        }
                    }
                }
            }
       // }
    }
}