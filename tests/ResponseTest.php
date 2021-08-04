<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Axiom\Rivescript\Rivescript;

abstract class ResponseTest extends TestCase
{
    /**
     * @var Rivescript
     */
    protected $rivescript;

    public function setUp(): void
    {
        $this->rivescript = new Rivescript();

        $this->rivescript->load(realpath('./tests/resources/test.rive'));
    }
}
