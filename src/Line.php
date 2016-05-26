<?php

namespace Vulcan\Rivescript;

class Line
{
    /**
     * @var string
     */
    protected $line;

    /**
     * @var int
     */
    protected $number;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $blocks = [
        'Comment',
        'Command',
        'Value'
    ];

    /**
     * @var bool
     */
    protected $interrupted = false;

    /**
     * @var bool
     */
    protected $isComment = false;

    /**
     * Create a new Line instance.
     *
     * @param string  $line
     */
    public function __construct($line, $number)
    {
        $this->line   = $line;
        $this->number = $number;

        $this->process();
    }

    protected function process()
    {
        $this->removeWhitespace();

        foreach ($this->blocks as $block) {
            $this->{'block'.$block}();
        }
    }

    public function number()
    {
        return $this->number;
    }

    public function command()
    {
        return $this->command;
    }

    public function value()
    {
        return $this->value;
    }

    protected function blockComment()
    {
        if ($this->startsWith('//')) {
            $this->interrupted = true;
        } elseif ($this->startsWith('#')) {
            // $this->warning('Using the # symbol for comments is deprecated');
            $this->interrupted = true;
        } elseif ($this->startsWith('/*')) {
            if ($this->endsWith('*/')) return null;

            $this->isComment = true;
        } elseif ($this->endsWith('*/')) {
            $this->isComment = false;
        }
    }

    protected function blockCommand()
    {
        if (strlen($this->line) < 2) {
            // $this->warning("Weird single-character line #$linenumber found.");
            $this->interrupted = true;
            return;
        }

        $this->command = substr($this->line, 0, 1);
    }

    protected function blockValue()
    {
        $this->value = trim(substr($this->line, 1));
    }

    public function isComment()
    {
        return $this->isComment;
    }

    public function isInterrupted()
    {
        return $this->interrupted;
    }

    /**
     * Trim leading and trailing whitespace as well as
     * whitespace surrounding individual arguments.
     *
     * @return string
     */
    public function removeWhitespace()
    {
        $this->line = trim($this->line);
        $this->line = preg_replace('/( )+/', ' ', $this->line);
    }

    /**
     * Determine if string starts with the supplied needle.
     *
     * @param string  $needle
     * @return bool
     */
    public function startsWith($needle)
    {
        return $needle === '' or strrpos($this->line, $needle, -strlen($this->line)) !== false;
    }

    /**
     * Determine if string ends with the supplied needle.
     *
     * @param string  $needle
     * @return bool
     */
    public function endsWith($needle)
    {
        return $needle === '' or (($temp = strlen($this->line) - strlen($needle)) >= 0 and strpos($this->line, $needle, $temp) !== false);
    }
}
