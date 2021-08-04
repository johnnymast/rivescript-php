<?php

use Axiom\Rivescript\Rivescript;

uses()->beforeEach(function () {

    $this->rivescript = new Rivescript();
    $this->rivescript->load(realpath(__DIR__.'./resources/tags/tags.rive'));

} )->in('Cortex/Tags');
