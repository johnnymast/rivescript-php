<?php

namespace Axiom\Rivescript\Cortex;

class Node
{
    /**
     * @var string
     */
    protected $source;

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
     * @var bool
     */
    protected $isInterrupted = false;

    /**
     * @var bool
     */
    protected $isComment = false;

    /**
     * @var bool
     */
    protected $allowUtf8 = false;


    /**
     * Create a new Source instance.
     *
     * @param  string  $source
     * @param  int  $number
     */
    public function __construct($source, int $number)
    {
        $this->source = remove_whitespace($source);
        $this->number = $number;

        $this->determineComment();
        $this->determineCommand();
        $this->determineValue();
    }

    /**
     * Returns the node's command trigger.
     *
     * @return string
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * Returns the node's value.
     *
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Returns the node's line number.
     *
     * @return int
     */
    public function number()
    {
        return $this->number;
    }

    /**
     * Returns true if node is a comment.
     *
     * @return bool
     */
    public function isComment()
    {
        return $this->isComment;
    }

    /**
     * Returns true is node has been interrupted.
     *
     * @return bool
     */
    public function isInterrupted()
    {
        return $this->isInterrupted;
    }

    /**
     * Determine the command type of the node.
     *
     * @return void
     */
    protected function determineCommand()
    {
        if (mb_strlen($this->source) === 0) {
            $this->isInterrupted = true;

            return;
        }

        $this->command = mb_substr($this->source, 0, 1);
    }

    /**
     * Determine if the current node source is a comment.
     *
     * @return void
     */
    protected function determineComment()
    {
        if (starts_with($this->source, '//')) {
            $this->isInterrupted = true;
        } elseif (starts_with($this->source, '#')) {
            log_warning('Using the # symbol for comments is deprecated');
            $this->isInterrupted = true;
        } elseif (starts_with($this->source, '/*')) {
            if (ends_with($this->source, '*/')) {
                return;
            }
            $this->isComment = true;
        } elseif (ends_with($this->source, '*/')) {
            $this->isComment = false;
        }
    }

    /**
     * Determine the value of the node.
     *
     * @return void
     */
    protected function determineValue()
    {
        $this->value = trim(mb_substr($this->source, 1));
    }

    /**
     * Check the syntax
     *
     * @return string
     */
    public function checkSyntax()
    {
        if (starts_with($this->source, '!')) {
            # ! Definition
            #   - Must be formatted like this:
            #     ! type name = value
            #     OR
            #     ! type = value
            #   - Type options are NOT enforceable, for future compatibility; if RiveScript
            #     encounters a new type that it can't handle, it can safely warn and skip it.
            if ($this->matchesPattern("/^.+(?:\s+.+|)\s*=\s*.+?$/", $this->source) === false) {
                return "Invalid format for !Definition line: must be '! type name = value' OR '! type = value'";
            }
        } elseif (starts_with($this->source, '>')) {
            # > Label
            #   - The "begin" label must have only one argument ("begin")
            #   - "topic" labels must be lowercase but can inherit other topics ([A-Za-z0-9_\s])
            #   - "object" labels follow the same rules as "topic" labels, but don't need be lowercase
            if ($this->matchesPattern("/^begin/", $this->value) == true
                && $this->matchesPattern("/^begin$/", $this->value) === false) {
                return "The 'begin' label takes no additional arguments, should be verbatim '> begin'";
            } elseif ($this->matchesPattern("/^topic/", $this->value) == true
                && $this->matchesPattern("/[^a-z0-9_\-\s]/", $this->value) == true) {
                return "Topics should be lowercased and contain only numbers and letters!";
            } elseif ($this->matchesPattern("/^object/", $this->value) == true
                && $this->matchesPattern("/[^a-z0-9_\-\s]/", $this->value) == true) {
                return "Objects can only contain numbers and lowercase letters!";
            }
        } elseif (starts_with($this->source, '+') || starts_with($this->source, '%')
            || starts_with($this->source, '@')) {

            # + Trigger, % Previous, @ Redirect
            #   This one is strict. The triggers are to be run through Perl's regular expression
            #   engine. Therefore it should be acceptable by the regexp engine.
            #   - Entirely lowercase
            #   - No symbols except: ( | ) [ ] * _ # @ { } < > =
            #   - All brackets should be matched

            if ($this->allowUtf8 == true) {
                if ($this->matchesPattern("/[A-Z\\.]/", $this->value) == true) {
                    return "Triggers can't contain uppercase letters, backslashes or dots in UTF-8 mode.";
                }
            } else {
                if ($this->matchesPattern("/[^a-z0-9(\|)\[\]*_#\@{}<>=\s]/", $this->value) == true) {
                    return "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > =";
                }
            }

            $parens = 0; # Open parenthesis
            $square = 0; # Open square brackets
            $curly = 0; # Open curly brackets
            $chevron = 0; # Open angled brackets

            for ($i = 0; $i < strlen($this->value); $i++) {
                $chr = $this->value[$i];

                # Count brackets.
                if ($chr == '(') $parens++ ; if ($chr == ')') $parens--;
                if ($chr == '[') $square++ ; if ($chr == ']') $square--;
                if ($chr == '{') $curly++ ; if ($chr == '}') $curly--;
                if ($chr == '<') $chevron++ ; if ($chr == '>') $chevron--;
            }

            if ($parens) {
                return "Unmatched " . ($parens > 0 ? "left" : "right") . " parenthesis bracket ()";
            }
            if ($square) {
                return "Unmatched " . ($square > 0 ? "left" : "right") . " square bracket []";
            }
            if ($curly) {
                return "Unmatched " . ($curly > 0 ? "left" : "right") . " curly bracket {}";
            }
            if ($chevron) {
                return "Unmatched " . ($chevron > 0 ? "left" : "right" ) . " angled bracket <>";
            }
        } elseif (starts_with($this->source, '-') || starts_with($this->source, '^')
            || starts_with($this->source, '/')) {
            # - Trigger, ^ Continue, / Comment
            # These commands take verbatim arguments, so their syntax is loose.
        } elseif (starts_with($this->source, '*') === true && $this->isComment() == false) {
            # * Condition
            #   Syntax for a conditional is as follows:
            #   * value symbol value => response
            if ($this->matchesPattern("/^.+?\s*(==|eq|!=|ne|<>|<|<=|>|>=)\s*.+?=>.+?$/", $this->value) == false) {
                return "Invalid format for !Condition: should be like `* value symbol value => response`";
            }
        }
    }

    /**
     * Check for patterns in a given string.
     *
     * @param  string  $regex  The pattern to detect.
     * @param  string  $string  The string that could contain the pattern.
     *
     * @return bool
     */
    private function matchesPattern(string $regex = '', string $string = ''): bool
    {
        preg_match_all($regex, $string, $matches);

        return isset($matches[0][0]);
    }
}
