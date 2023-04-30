<?php

require __DIR__ . '/../vendor/autoload.php';

use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\RivescriptEvent;

$code = <<<EOF
! version = 2.00
! global debug = 1

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
  
    > begin

    + request
    * <get name> == undefined => {topic=newuser}{ok}
    - {ok}

  < begin
  
+ what (are|is) you
- I am a robot.

> topic newuser
    + *
    - Hello! My name is <bot name>! I'm a robot. What's your name?

    + _
    % * what is your name
    - <set name=<formal>>Nice to meet you, <get name>!{topic=random}
  < topic
  
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

> object encode2 perl
use Digest::MD5 qw(md5_hex);
use MIME::Base64 qw(encode_base64);

< object

EOF;

function write($msg): void
{
    echo "{$msg}\n";
}

$rivescript = new Rivescript(debug: true);
$rivescript->on(RivescriptEvent::DEBUG, fn(string $msg) => write($msg));
$rivescript->on(RivescriptEvent::VERBOSE, fn(string $msg) => write($msg));
$rivescript->sortReplies();
$rivescript->stream($code);
