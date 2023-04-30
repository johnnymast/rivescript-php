<?php

require __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Rivescript;

$rivescript = new Rivescript(debug: true);
//$rivescript->loadFile(__DIR__.'/../tests/brain.rive');
$rivescript->setUserVars(data: ['username1' => ['name' => 'bob'], 'username2' => ['name' => 'alice']]);
$rivescript->sortReplies();
$x = $rivescript->getUserVars('username1');
print_r($x);