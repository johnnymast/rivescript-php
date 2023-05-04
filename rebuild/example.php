<?php

require __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\RivescriptEvent;

$code = <<<EOF
! version = 2.00
! global debug = 1
! local bleep = blap

  ! var name      = RiveScript Bot
  ! var age       = 0
  ! var gender    = androgynous
  ! var location  = Cyberspace
  ! var generator = RiveScript
  
  ! array colors = red green blue cyan magenta yellow black white orange brown
  
  ! sub what're = what are
  ! sub what'd  = what did
  ! sub a/s/l   = age sex location
  ! sub brb     = be right back
  ! sub afk     = away from keyboard
  ! sub l o l   = lol
  
    ! person you are = I am
  ! person i am    = you are
  ! person you     = I
  ! person i       = you
  
  
  + knock knock
- Who's there?

+ *
% who is there
- <set joke=<star>><sentence> who?

+ <get joke> *
- Haha! "{sentence}<get joke> <star>{/sentence}"! :D

    > begin

    + request
    * <get name> == undefined => {topic=newuser}{ok}
    - {ok}

  < begin
  
+ what (are|is) you
- I am a robot.

+ bleep
@ hi

  + my name iss *
  * <get name> eq <star>    => I know, you told me that already.
  * <get name> ne undefined => Did you get a name change?<set name=<star>>
  - <set name=<star>>Nice to meet you, <star>.
  
> topic newuser
    + *
    - Hello! My name is <bot name>! I'm a robot. What's your name?

    + _
    % * what is your name
    - <set name=<formal>>Nice to meet you, <get name>!{topic=random}
  < topic
  
  
> object encode perl
use Digest::MD5 qw(md5_hex);
use MIME::Base64 qw(encode_base64);

< object

> object encode2 php
phpinfo();
< object

EOF;




class TermBuffer
{
    protected array $items = [];

    public function __construct(protected readonly int $max, protected bool $skip = false)
    {
    }

    public function setSkip(bool $value): void
    {
        $this->skip = $value;
    }

    private function pop(): void
    {
        array_shift($this->items);
    }

    public function push(string $value): void
    {
        if (count($this->items) < $this->max) {
            $this->items [] = $value;
        } else {
            $this->pop();
        }
    }

    public function output(): void
    {
        foreach ($this->items as $index => $item) {
            echo $item . "\n";
        }

        if (!$this->skip) {
            echo str_repeat("\033[F", min($index + 1, $this->max));
        }
    }
}

const FMT_ERROR = "\033[31m%s \033[0m";
;
const FMT_DEBUG = "\033[32m%s \033[0m";
const FMT_WARN = "\033[33m%s \033[0m";
const FMT_INFO = "\033[36m%s \033[0m";

$useBuffer = false;

if ($useBuffer) {
    $buffer = new TermBuffer(5);
} else {
    $buffer = null;
}

function write($msg, $buffer, $fmt): void
{
    if ($buffer) {
        $buffer->push(sprintf($fmt, $msg));
        $buffer->output();
    } else {
        echo sprintf($fmt, $msg) . "\n";
    }
}

$rivescript = new Rivescript(debug: true);
$rivescript->on(RivescriptEvent::WARNING, fn(string $msg) => write($msg, $buffer, FMT_WARN));
$rivescript->on(RivescriptEvent::ERROR, fn(string $msg) => write($msg, $buffer, FMT_ERROR));
$rivescript->on(RivescriptEvent::SAY, fn(string $msg) => write($msg, $buffer, FMT_INFO));
$rivescript->on(RivescriptEvent::DEBUG, fn(string $msg) => write($msg, $buffer, FMT_DEBUG));

$rivescript->sortReplies();

$rivescript->stream($code);

if ($buffer) {
    $buffer->setSkip(true);
    $buffer->output();
}