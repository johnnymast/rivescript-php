<?php

namespace Axiom\Rivescript\Cortex;

class RegExpressions
{
    public const TRIGGER_DEFINITION = "/^.+(?:\s+.+|)\s*=\s*.+?$/";

    public const LABEL_BEGIN_SYNTAX1 = "/^begin/";
    public const LABEL_BEGIN_SYNTAX2 = "/^begin$/";
    public const LABEL_TOPIC_SYNTAX1 = "/^topic/";
    public const LABEL_TOPIC_SYNTAX2 = "/[^a-z0-9_\-\s]/";

    public const LABEL_OBJECT_SYNTAX1 = "/^object/";
    public const LABEL_OBJECT_SYNTAX2 = "/[^a-z0-9_\-\s]/";

    public const DEFINITION_SYNTAX1 = "/^.+(?:\s+.+|)\s*=\s*.+?$/";

    public const TRIGGER_SYNTAX1 = "/[A-Z\\.]/";
    public const TRIGGER_SYNTAX2 = "/[^a-z0-9(\|)\[\]*_#\@{}<>=\s]/";

    public const REDIRECT_SYNTAX1 = "/[A-Z\\.]/";
    public const REDIRECT_SYNTAX2 = "/[^a-z0-9(\|)\[\]*_#\@{}<>=\s]/";

    public const PREVIOUS_SYNTAX1 = "/[A-Z\\.]/";
    public const PREVIOUS_SYNTAX2 = "/[^a-z0-9(\|)\[\]*_#\@{}<>=\s]/";


    public const CONDITION_SYNTAX1 = "/.+?\s(==|eq|!=|ne|<>|<|<=|>|>=)\s.+?=>.+?$/";


    public const TRIGGER_DETECT_ARRAY = "/\@(\w+)/";
    public const TRIGGER_DETECT_ALTERNATION = "/(\()(?!\@)(.+?=*)(\))/iu";
    public const TRIGGER_DETECT_OPTIONAL = "/(\[)(?!\@)(.+?=*)(\])/ui";
    public const TRIGGER_DETECT_PRIORITY = "/{weight=(.+?)}/";

    public const RESPONSE_DETECT_WEIGHT = "/{weight=(.+?)}/";
    public const RESPONSE_RANDOM_ARRAY_VALUE = "/\(@(.+?)\)/ui";

    public const TAG_STAR = "/<star(\d+)?>/i";
    public const TAG_INPUT = "/<input(\d+)?>/i";
    public const TAG_REPLY = "/<reply(\d+)?>/i";
    public const TAG_ID = "/<id>/u";
    public const TAG_CHARS = "/(\\n|\\s|\\#|\\/)/u";
    public const TAG_BOT = "/<bot (.+?)=(.+?)>\b|<bot (.+?)>/u";
    public const TAG_ENV = "/<env (.+?)=(.+?)>\b|<env (.+?)>/u";
    public const TAG_RANDOM = "/\{random\}(.+?){\/random\}/u";
    public const TAG_PERSON = "/({)person(})(.+?)({)\/person(})|(<)person(>)/u";
    public const TAG_FORMAL = "/({)formal(})(.+?)({)\/formal(})|(<)formal(>)/u";
    public const TAG_SENTENCE = "/({)sentence(})(.+?)({)\/sentence(})|(<)sentence(>)/u";
    public const TAG_UPPERCASE = "/({)uppercase(})(.+?)({)\/uppercase(})|(<)uppercase(>)/u";
    public const TAG_LOWERCASE = "/({)lowercase(})(.+?)({)\/lowercase(})|(<)lowercase(>)/u";
    public const TAG_SET = "/<set (.+?)=(.+?)>/U";





    public const TAG_GET = "/<get (.+?)>/u";

//    public const TAG_ARRAYS = "/\(@(.+?)\)/ui";
}
