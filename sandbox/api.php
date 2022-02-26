<?php
include '../vendor/autoload.php';

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Rivescript;

$script =<<<EOF
+ hi bot
- hello human
EOF;

function onVerbose($msg) {
    echo "{$msg}\n";
};

function onDebug($msg) {
    echo "{$msg}\n";
};

$rivescript = new Rivescript([

]);


$rivescript->stream($script);

$rivescript->on(Event::DEBUG, 'onDebug')
           ->on(Event::DEBUG_VERBOSE, 'onVerbose');


echo $rivescript->reply("hi bot");