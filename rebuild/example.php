<?php
require __DIR__.'/../vendor/autoload.php';

use Axiom\Rivescript\Rivescript;

$rivescript = new Rivescript(debug: true);
$rivescript->loadFile(__DIR__.'/../tests/brain.rive');
