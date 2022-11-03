<?php

namespace Axiom\Rivescript\Cortex\ResponseQueue;

use Axiom\Collections\Collection;
use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\Commands\ResponseAbstract;
use Axiom\Rivescript\Cortex\Commands\ResponseInterface;
use Axiom\Rivescript\Cortex\TagRunner;
use Axiom\Rivescript\Cortex\Tags\Tag;

class ResponseQueueItem
{
    /**
     * @var \Axiom\Collections\Collection<ResponseInterface>
     */
    protected Collection $continues;

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseInterface $command The command for this queue item.
     * @param int                                                 $order   the order for this queue item.
     */
    public function __construct(
        protected ResponseInterface $command,
        protected int               $order = 0,
    )
    {
        $this->continues = Collection::make([]);
    }

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseInterface $command
     *
     * @return void
     */
    public function addContinue(ResponseInterface $command): void
    {
        $this->continues->push($command);
    }

    /**
     * Render the output of this item.
     *
     * @return string
     */
    public function render(): string
    {

        $this->prepare($this->command);

        $content = $this->command
            ->getNode()
            ->getContent();

        if ($this->continues->count() > 0) {

            $concat = synapse()->memory->local()->get('concat');

            foreach ($this->continues as $response) {
                $options = $response->getOptions();
                $concat = $options['concat'];

                $this->prepare($response);
                $continue = $response->getNode()->getContent();

                $content .= match ($concat) {
                    'space' => " {$continue}",
                    'newline' => "\n{$continue}",
                    default => $continue
                };
            }
        }

        return $content;
    }

    /**
     * Return the tag runner on this command.
     *
     * @param \Axiom\Rivescript\Cortex\Commands\ResponseInterface $response
     *
     * @return void
     */
    private function prepare(ResponseInterface $response): void
    {
        $response->invokeStars();
        TagRunner::run(Tag::RESPONSE, $response);
    }

    /**
     * Validate if this queue item
     * is ready to be used yes/no.
     *
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }
}