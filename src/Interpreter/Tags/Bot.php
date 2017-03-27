<?php

namespace Vulcan\Rivescript\Interpreter\Tags;

class Bot
{
    /**
     * @var Object
     */
    protected $pattern;

    /**
     * Create a new Bot instance.
     */
    public function __construct()
    {
        $this->pattern = regex()
            ->find('<bot ')
            ->openGroup()
                ->anythingBut('>')
            ->closeGroup()
            ->then('>')
            ->stopAtFirst();
    }

    /**
     * Parse the source.
     *
     * @param  String  $source
     * @return String
     */
    public function parse($source)
    {
        if ($this->pattern->test($source)) {
            $matches  = $this->pattern->match($source);

            $source = $this->pattern->replace($source, synapse()->memory->variables()->get($matches[1]));
        }

        return $source;
    }
}
