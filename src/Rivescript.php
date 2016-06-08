<?php

namespace Vulcan\Rivescript;

class Rivescript extends Utility
{
    /**
     * @var Parser
     */
    protected $parser;

    public $tree;

    protected $metadata = [
        'topic' => 'random',
    ];

    protected $tags = [
        'Topic',
        'Star'
    ];

    protected $triggers = [
        'Atomic',
        'Alternation',
        'Wildcard'
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
        $message  = $this->prepareMessage($message);
        $triggers = $this->tree['topics'][$this->getMetadata('topic')]['triggers'];

        if (count($triggers) > 0) {
            foreach ($triggers as $key => $trigger) {
                foreach ($this->triggers as $class) {
                    $class        = "\Vulcan\Rivescript\Triggers\\$class";
                    $triggerClass = new $class;

                    $found = $triggerClass->parse($key, $trigger['trigger'], $message);

                    if (isset($found['match']) and $found['match'] === true) {
                        break 2;
                    }
                }
            }

            if (isset($found['key']) and is_int($found['key'])) {
                $replies = $triggers[$found['key']]['reply'];

                if (isset($triggers[$found['key']]['redirect'])) {
                    return $this->reply($user, $triggers[$found['key']]['redirect']);
                }

                if (count($replies)) {
                    $key   = array_rand($replies);
                    $reply = $this->parseReply($replies[$key], $found['data']);

                    return $reply;
                }
            }
        }

        return 'No response found.';
    }

    public function parseReply($message, $data = array())
    {
        $message = [
            'response' => $message,
            'metadata' => []
        ];

        foreach ($this->tags as $class) {
            $class    = "\Vulcan\Rivescript\Tags\\$class";
            $tagClass = new $class;

            $message = $tagClass->parse($message['response'], $data);

            $this->syncMetadata($message['metadata']);
        }

        return $message['response'];
    }

    public function syncMetadata($collection)
    {
        foreach ($collection as $key => $value) {
            $this->setMetadata($key, $value);
        }
    }

    public function getMetadata($key)
    {
        return $this->metadata[$key];
    }

    public function setMetadata($key, $value)
    {
        $this->metadata[$key] = $value;
    }

    protected function prepareMessage($message)
    {
        $message = strtolower($message);

        foreach ($this->tree['begin']['sub'] as $find => $replace) {
            $message = str_replace($find, $replace, $message);
        }

        $message = preg_replace("/[^A-Za-z0-9 ]/", '', $message);

        return $message;
    }
}
