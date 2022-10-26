<?php
include __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Exceptions\ParseException;
use Axiom\Rivescript\Rivescript;

$script = <<<EOF
  ! person you are = I am
  ! person i am    = you are
  ! person you     = I
  ! person i       = you
  ! person mine    = yours

  + test1
  - Umm... {person}you{/person}

  
  + say *
  - Umm... yo "<person>"
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

    echo $rivescript->reply("test1");
    echo $rivescript->reply("say mine");

} catch (ParseException $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

