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
        'Topic',
        'Stars'
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
                    $class        = "\Vulcan\Rivescript\Interpreter\Triggers\\$class";
                    $triggerClass = new $class;

                    $found = $triggerClass->parse($key, $trigger['trigger'], $message);

                    if (isset($found['match']) and $found['match'] === true) {
                        break 2;
                    }
                }
            }

            if (isset($found['key']) and is_int($found['key'])) {
                $replies = $triggers[$found['key']]['reply'];

                if (count($replies)) {
                    $reply = $this->parseReply($replies[array_rand($replies)], $found['data']);

                    return $reply;
                }
            }
        }

        return 'No response found.';
    }

    public function parseReply($message, $data = array())
    {
        foreach ($this->tags as $type) {
            $message = $this->{'tag'.$type}($message, $data);
        }

        return $message;
    }

    protected function tagTopic($text, $data)
    {
        $pattern = '/{\s*topic\s*=\s*(\w+)\s*}/i';

        preg_match_all($pattern, $text, $matches);

        if (! empty($matches[1])) {
            $text        = preg_replace($pattern, '', $text);
            $this->topic = $matches[1][0];
        }

        return $text;
    }

    protected function tagStars($text, $data)
    {
        $pattern = '/<star(([0-9])?)>/';

        preg_match_all($pattern, $text, $matches);

        if (isset($matches[1])) {
            $search = $matches[0];

            foreach ($matches[1] as $match) {
                if (empty($match)) {
                    $match = 0;
                } else {
                    $match--;
                }

                if (isset($data['stars'][$match])) {
                    $replace[] = $data['stars'][$match];
                } else {
                    $replace[] = '';
                }
            }

            if (isset($replace)) {
                $text = str_replace($search, $replace, $text);
            }
        }

        return $text;
    }
}
