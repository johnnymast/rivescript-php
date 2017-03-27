<?php

namespace Vulcan\Rivescript\Interpreter;

class Input
{
    /**
     * @var String
     */
    protected $source;

    /**
     * @var String
     */
    protected $original;

    /**
     * @var Integer|Null
     */
    protected $user;

    /**
     * Create a new Input instance.
     *
     * @param  String  $source
     * @param  Integer|Null  $user
     */
    public function __construct($source, $user = null)
    {
        $this->original = $source;
        $this->user     = $user;

        $this->cleanOriginalSource();
    }

    public function source()
    {
        return $this->source;
    }

    public function user()
    {
        return $this->user;
    }

    /**
     * Clean the source input, so its in a state easily readable
     * by the interpreter.
     *
     * @return Void
     */
    protected function cleanOriginalSource()
    {
        $this->source = mb_strtolower($this->original);

        synapse()->memory->substitute()->each(function($replace, $needle) {
            $pattern = '/\b\\'.$needle.'\b';

            dd($pattern);
            $this->source = preg_replace($pattern, $replace, $this->source);
        });

        $this->source = remove_whitespace($this->source);
        $this->source = preg_replace('/[^\pL\d\s]+/u', '', $this->source);

        var_dump($this->source);
    }
}
