<?php
include __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Exceptions\ParseException;
use Axiom\Rivescript\Rivescript;

$script = <<<EOF


! global test = value

+ set 
- <env test=abc>Lol

+ test
- my name is <env test>
EOF;

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

$rivescript = new Rivescript();


try {
    $rivescript->stream($script);

    $rivescript->on(Event::DEBUG, 'onDebug')
        ->on(Event::DEBUG_WARNING, 'onVerbose');

//    $rivescript->reply("set");
    echo $rivescript->reply("test");

} catch (ParseException $e) {
    echo "Exception: ".$e->getMessage()."\n";
}

