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


try {
    $rivescript = new Rivescript(utf8: true, depth: 50);
    $rivescript->stream($script);

} catch (\Axiom\Rivescript\Exceptions\ParseException $e) {
} catch (\Axiom\Rivescript\Exceptions\ContentLoadingException $e) {
}

$rivescript->on(Event::DEBUG, 'onDebug')
    ->on(Event::SAY, 'onVerbose');


$tests = [
    "hello bot",

];

foreach ($tests as $msg) {
    $reply = $rivescript->reply($msg);
    echo $reply . "\n";
}
