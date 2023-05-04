<?php

namespace Axiom\Rivescript\Interfaces\Handlers;

use Axiom\Rivescript\Rivescript;

interface HandlerInterface
{
    /**
     * Load the code object.
     *
     * @param string          $name The name of the object.
     * @param string|callable $code The code for the object or a closure object.
     *
     * @return void
     */
    public function load(string $name, mixed $code): void;

    /**
     * Execute the code.
     *
     * @param string $name   the name of the object being called.
     * @param array  $fields array of arguments passed to the object.
     *
     * @return string|null
     */
    public function call(Rivescript $rs, string $name, array $fields = []): string|null;
}