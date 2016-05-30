<?php

namespace Vulcan\Rivescript;

class Rivescript extends Utility
{
    /**
     * @var Parser
     */
    protected $parser;

    public $tree;

    protected $topic = 'random';

    protected $tags = [
        'Topic'
    ];

    protected $triggers = [
        'Atomic',
        'Wildcard',
    ];

    /**
     * Create a new Rivescript instance.
     *
     * @param Parser  $parser
     */
    public function __construct()
    {
        parent::__construct();

        $this->parser = new Parser;
    }

    /*
    |--------------------------------------------------------------------------
    | Loading and Parsing Methods
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Load a RiveScript document from a file.
     *
     * @param array|string  $file
     */
    public function loadFile($file)
    {
        $this->tree = $this->parser->process($file);
    }

    public function reply($user, $message)
    {
        $triggers = $this->tree['topics'][$this->topic]['triggers'];

        if (count($triggers) > 0) {
            foreach ($triggers as $key => $trigger) {
                foreach ($this->triggers as $class) {
                    $className    = "\Vulcan\Rivescript\Interpreter\Triggers\\$class";
                    $triggerClass = new $className;

                    $found = $triggerClass->parse($trigger['trigger'], $message);

                    if ($found === true) {
                        $foundKey = $key;
                        break 2;
                    }
                }
            }

            if (isset($foundKey) and is_int($foundKey)) {
                $replies = $triggers[$foundKey]['reply'];

                if (count($replies)) {
                    $reply = $this->parseReply($replies[array_rand($replies)]);

                    return $reply;
                }
            }
        }

        return 'No response found.';
    }

    public function parseReply($message)
    {
        foreach ($this->tags as $type) {
            $message = $this->{'tag'.$type}($message);
        }

        return $message;
    }

    protected function tagTopic($text)
    {
        $pattern = '/{\s*topic\s*=\s*(\w+)\s*}/i';

        preg_match_all($pattern, $text, $matches);

        if (! empty($matches[1])) {
            $text        = preg_replace($pattern, '', $text);
            $this->topic = $matches[1][0];
        }

        return $text;
    }
}
