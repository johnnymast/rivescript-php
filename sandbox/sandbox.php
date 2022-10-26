<?php
include '../vendor/autoload.php';

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Rivescript;


$script =<<<EOF
+ hello (bot)
- hi human
EOF;

//$script = file_get_contents("source.rive");


function onVerbose($msg)
{
    echo "{$msg}\n";
}

;

function onDebug($msg)
{
    echo "{$msg}\n";
}

;

$rivescript = new Rivescript(utf8: true, depth: 50);

$rivescript->stream($script);

$rivescript->on(Event::DEBUG, 'onDebug')
    ->on(Event::SAY, 'onVerbose');


$tests = [
    "hello bot",

];

foreach ($tests as $msg) {
    $reply = $rivescript->reply($msg);
    echo $reply . "\n";
}
