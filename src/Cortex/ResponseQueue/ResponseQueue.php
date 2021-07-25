<?php

/**
 * The ResponseQueue sorts responses order and
 * determines what responses are valid or not.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     ResponseQueue
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\ResponseQueue;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Input;
use Axiom\Rivescript\Cortex\Node;

/**
 * Class ResponseQueue
 * @package Axiom\Rivescript\Cortex\ResponseQueue
 */
class ResponseQueue extends Collection
{

    /**
     * A container with responses.
     *
     * @var Collection
     */
    protected $responses;

    /**
     * ResponseQueue constructor.
     */
    public function __construct()
    {
        $this->responses = new Collection([]);
    }

    /**
     * Attach a response to the queue.
     *
     * @param  Node  $node  The node contains information about the command.
     */
    public function attach(Node $node)
    {
        $type = $this->determineResponseType($node->source());

        $this->responses->put($node->value(), new ResponseQueueItem($node->command(), $type, 0));
    }

    /**
     * Sort the responses by order.
     *
     * @param  Collection  $responses  The array containing the resources.
     * @return Collection
     */
    private function sortResponses(Collection $responses): Collection
    {
        return $responses->sort(
            function ($current, $previous) {
                return ($current->order < $previous->order) ? -1 : 1;
            }
        )->reverse();
    }

    /**
     * Check if a response is allowed to be returned by the bot or not.
     *
     * @param  string             $response  The response to validate.
     * @param  ResponseQueueItem  $item      The ResponseQueueItem.
     * @return false|mixed
     */
    private function validateResponse(string $response, ResponseQueueItem $item)
    {
        $response = $this->parseTags($response, synapse()->input);

        foreach (synapse()->responses as $class) {
            if (ucfirst($item->type) == $class and class_exists("\\Axiom\\Rivescript\\Cortex\\Responses\\{$class}")) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Responses\\{$class}";
                $class = new $class($response, $item);

                $result = $class->parse();

                if ($result) {
                    return $result;
                }
            }
        }

        return false;
    }

    /**
     * Parse the response through the available tags.
     *
     * @param  string  $response  The response string to parse.
     * @param  Input   $input     Input contains information about the user.
     *
     * @return string
     */
    protected function parseTags(string $response, Input $input): string
    {
        synapse()->tags->each(
            function ($tag) use (&$response, $input) {
                $class = "\\Axiom\\Rivescript\\Cortex\\Tags\\$tag";
                $tagClass = new $class('trigger');

                $response = $tagClass->parse($response, $input);
            }
        );

        return mb_strtolower($response);
    }

    /**
     * Determine the order of responses by type.
     *
     * @param  Collection  $responses  The responses to inspect.
     * @return Collection
     */
    private function determineResponseOrder(Collection $responses): Collection
    {
        return $responses->each(
            function ($data, $response) use ($responses) {
                if (isset($data->type)) {
                    switch ($data->type) {
                        case 'condition':
                            $data->order += 3000000;
                            break;
                        case 'weighted':
                        case 'atomic':
                            $data->order += 1000000;
                            break;
                    }

                    $responses->put($response, $data);
                }
            }
        );
    }

    /**
     * Determine the response type.
     *
     * @param  string  $response
     * @return string
     */
    public function determineResponseType(string $response): string
    {
        $wildcards = [
            'weighted' => '{weight=(.+?)}',
            'condition' => '/^\*/',
            'atomic' => '/^-/',
        ];

        foreach ($wildcards as $type => $pattern) {
            if (@preg_match_all($pattern, $response, $matches)) {
                return $type;
            }
        }

        return 'atomic';
    }

    /**
     * Process the Response Queue.
     *
     * @return false|int|string|null
     */
    public function process()
    {
        $this->responses = $this->determineResponseOrder($this->responses);

        $validResponses = new Collection([]);
        foreach ($this->responses as $response => $item) {
            $result = $this->validateResponse($response, $item);

            if ($result !== false) {
                $validResponses->put($result, $item);
            }
        }

        // TODO: multiple atomic makes it random
        // FIXME: Handle if returned false.

        $validResponses = $this->sortResponses($validResponses);

        print_r($validResponses);


        if ($validResponses->count() > 0) {
            return key($validResponses->first());
        }

        return false;
    }
}