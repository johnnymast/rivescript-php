<?php
include __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Exceptions\ParseException;
use Axiom\Rivescript\Rivescript;


$code = <<<EOF
+ what (are|is) you
- I am a robot.
EOF;

//
//+ hi * *
//- you said <star> <star2>

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
    $rivescript->stream($code);

//    echo $rivescript->getObjectMacroManager()->executeMacro("php", $code);
//    echo $rivescript->reply("hi me");
//    echo $rivescript->reply("what color is my red jacket");
//    echo $rivescript->reply("hi man girl");
    echo $rivescript->reply("what are you");
//    echo $rivescript->reply("My name is Bob");
//    echo $rivescript->reply("test B");
//    echo "\n=====================\n";
//    echo $rivescript->reply("add");
//    echo $rivescript->reply("show");

    $rivescript->on(Event::DEBUG, 'onDebug')
        ->on(Event::DEBUG_WARNING, 'onVerbose');

//    echo $rivescript->reply("method1");
//    echo $rivescript->reply("say i am cool");
//    echo $rivescript->reply("set debug mode true");

} catch (ParseException $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

