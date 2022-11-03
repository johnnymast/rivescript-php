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

//use Axiom\Rivescript\Cortex\ResponseQueue\ResponseQueueItem;
//use Axiom\Rivescript\Traits\Regex;
//use Axiom\Rivescript\Traits\Tags;
//use Axiom\Rivescript\Cortex\TriggerCommand as TriggerData;

/**
 * Output class
 *
 * Processes the output for the bot user.
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
class Output
{
    /**
     * The output string
     *
     * @var string
     */
    protected string $output = '';

    /**
     * Keep track of the recursion
     * in the output.
     *
     * @var int
     */
    protected int $recursion = 0;

    /**
     * Error messages.
     *
     * @var array|string[]
     */
    public array $errors = [
        "replyNotMatched" => "ERR: No Reply Matched",
        "replyNotFound" => "ERR: No Reply Found",
        "objectNotFound" => "[ERR: Object Not Found]",
        "deepRecursion" => "ERR: Deep Recursion Detected"
    ];

    /**
     *
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return void
     */
    private function reset(): void
    {
        $this->output = '';
        $this->recursion = 0;
    }


    /**
     * Procress the Topic.
     *
     * @param int $recursion The resuroppm level.
     *
     * @return string
     */
    private function processTopic(int $recursion): string
    {
        if ($recursion === synapse()->memory->global()->get('depth')) {
            synapse()->rivescript->error("Top many recursive calls to :func", ["func" => __CLASS__ . "::" . __FUNCTION__]);
            return $this->errors["deepRecursion"];
        }

        $recursion++;

        $topicName = synapse()->memory->shortTerm()->get('topic') ?: 'random';
        $topic = synapse()->brain->topic($topicName);

        synapse()->rivescript->debug("TOPIC: :topic", ["topic" => $topicName]);

        // $this->processTopic($recursion);

        $trigger = $topic->detectTrigger();
        $result = $this->errors["replyNotFound"];

        if (is_null($trigger) === false) {
            $queue = $trigger->getQueue();
            if ($queue->hasResponses()) {
                return $queue->process();
            }
        }

        return $result;
    }

    /**
     * Process the input given by a user.
     *
     * @return string
     */
    public function processInput(): string
    {
        return $this->processTopic(recursion: 0);
    }
}
