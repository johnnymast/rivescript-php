<?php

namespace Axiom\Rivescript\Cortex\Commands;

use Axiom\Rivescript\Cortex\Node;

interface ResponseInterface
{
    public function setTrigger(TriggerCommand $trigger): void;

    public function getType(): string;
    public function getNode(): Node;

    public function getOptions(): array;

    public function invokeStars(): void;
}