<?php
include __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Exceptions\ParseException;
use Axiom\Rivescript\Rivescript;

$script = <<<EOF
+ say *
- Hello <person>
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

$rivescript = new Rivescript();


try {
    $rivescript->stream($script);

    $rivescript->on(Event::DEBUG, 'onDebug')
        ->on(Event::DEBUG_WARNING, 'onVerbose');

//    echo $rivescript->reply("method1");
    echo $rivescript->reply("say i am cool");
//    echo $rivescript->reply("set debug mode true");

} catch (ParseException $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

