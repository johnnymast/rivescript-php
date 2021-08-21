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
use Axiom\Rivescript\Cortex\Node;
use Axiom\Rivescript\Traits\Tags;

/**
 * Class ResponseQueue
 */
class ResponseQueue extends Collection
{

    use Tags;

    /**
     * A container with responses.
     *
     * @var Collection<ResponseQueueItem>
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
     * @param Node $node The node contains information about the command.
     *
     * @return void
     */
    public function attach(Node $node)
    {
        $type = $this->determineResponseType($node->source());

        $this->responses->put($node->value(), new ResponseQueueItem($node->command(), $type, 0));
    }

    /**
     * Sort the responses by order.
     *
     * @param Collection<ResponseQueueItem> $responses The array containing the resources.
     *
     * @return Collection<ResponseQueueItem>
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
     * @param string            $response The response to validate.
     * @param ResponseQueueItem $item     The ResponseQueueItem.
     *
     * @return false|mixed
     */
    private function validateResponse(string $response, ResponseQueueItem $item)
    {
        $response = $this->parseTags($response);

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
     * Merge the ^ continue responses to the last - response.
     *
     * @param Collection<ResponseQueueItem> $responses The array containing the responses.
     *
     * @return Collection<ResponseQueueItem>
     */
    protected function mergeContinues(Collection $responses): Collection
    {
        $lastData = $responses->first();
        $lastResponse = "";
        $responses->each(
            function (ResponseQueueItem $data, $response) use (&$lastData, &$lastResponse, &$responses) {

                if ($data->type == 'continue' && $lastData->command == '-') {
                    $responses->remove($lastResponse);
                    $responses->remove($response);

                    $lastResponse .= $response;
                    $responses->put($lastResponse, $lastData);
                }

                if ($data->command !== '^') {
                    $lastData = $data;
                    $lastResponse = $response;
                }
            }
        );

        return $responses;
    }

    /**
     * Determine the order of responses by type.
     *
     * @param Collection<ResponseQueueItem> $responses The responses to inspect.
     *
     * @return Collection<ResponseQueueItem>
     */
    private function determineResponseOrder(Collection $responses): Collection
    {
        return $responses->each(
            function (ResponseQueueItem $data, $response) use ($responses) {
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
     * @param string $response
     *
     * @return string
     */
    public function determineResponseType(string $response): string
    {
        $wildcards = [
            'weighted' => '{weight=(.+?)}',
            'condition' => '/^\*/',
            'continue' => '/^\^/',
            'atomic' => '/-/',
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
        $this->responses = $this->mergeContinues($this->responses);
        $this->responses = $this->determineResponseOrder($this->responses);

        $validResponses = new Collection([]);
        foreach ($this->responses as $response => $item) {
            $result = $this->validateResponse($response, $item);

            if ($result !== false) {
                $validResponses->put($result, $item);
            }
        }

        $validResponses = $this->sortResponses($validResponses);

        if ($validResponses->count() > 0) {
            return $validResponses->keys()->first();
        }

        return false;
    }
}
