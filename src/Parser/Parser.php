<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript\Parser;

use Axiom\Rivescript\{
    Exceptions\Parser\ParserException,
    Interfaces\Events\EventEmitterInterface,
    Messages\RivescriptMessage,
    Rivescript,
    RivescriptType,
    RivescriptEvent,
    Traits\EventEmitter,
    Traits\Regex,
    Utils\Str,
};

/**
 * Parser class
 *
 * This is a port of the rivescript-js parser written by Noah Petherbridge.
 * The original source code can be found here:
 *
 * https://github.com/aichaos/rivescript-js/blob/423cd4ac41ecc4f837639ee8bc8d2163bf61860b/src/parser.js
 *
 * PHP version 8.1 and higher.
 *
 * @category Core
 * @package  Parser
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Parser extends AbstractParser implements EventEmitterInterface
{
    use EventEmitter;
    use Regex;

    public const RS_VERSION = 2.0;

    protected array $values = [];

    /**
     * Parser constructor.
     *
     * @param \Axiom\Rivescript\Rivescript $master A reference to the parent RiveScript instance.
     * @param bool                         $strict Whether strict mode is enabled.
     * @param bool                         $utf8   Whether UTF-8 mode is enabled.
     */
    public function __construct(
        protected readonly Rivescript $master,
        protected readonly bool $strict = false,
        protected readonly bool $utf8 = false,
        protected readonly bool $forceCase = false // TODO:  Move to above here
    )
    {
    }

    /**
     * Reset the parser.
     *
     * @return array
     */
    public function reset(): array
    {
        $this->values = [
            "begin" => [
                "global" => [],
                "var" => [],
                "sub" => [],
                "person" => [],
                "array" => [],
            ],
            "topics" => [],
            "objects" => [],
        ];

        return $this->values;
    }

    /**
     * Output a message.
     *
     * @param \Axiom\Rivescript\Messages\RivescriptMessage $message The message to output.
     *
     * @return void
     */
    private function output(RivescriptMessage $message): void
    {
        $this->emit(RivescriptEvent::OUTPUT, $message);
    }

    /**
     * @throws \Axiom\Rivescript\Exceptions\Parser\ParserException
     */
    private function _error(RivescriptMessage $error)
    {
        $this->emit(RivescriptEvent::OUTPUT, $error);

        throw new ParserException("Error while parsing script.");
    }

    /**
     * Parse the RiveScript code.
     *
     * @param string $code The code in string.
     *
     * @throws \Axiom\Rivescript\Exceptions\Parser\ParserException
     * @return array<string, mixed>
     */
    public function parse(string $filename = "stream", string $code = ""): array
    {
        $this->reset(); // FIXME remove later

        $local_options = [
            "concat" => "none"
        ];
        $this->output(RivescriptMessage::Warning("implement local options"));

        $context = $this->createParserContext(); // FIXME remove later

        $tmp = explode("\n", $code);
        $lineno = 0;

        $lines = array_map(function ($line) use (&$lineno) {
            return ['script' => $line, 'lineno' => ++$lineno];
        }, $tmp);

        $this->output(RivescriptMessage::Warning("MAKE VARIABLES THE SAME AS THE PYTHON PARSER"));

        if (count($lines) > 0) {
            /**
             * First filter out the comments and objects.
             */
            for ($step = 0; $step < count($lines); $step++) {
                $record = $lines[$step];
                $script = trim($record['script']);
                $line = trim(substr($script, 1));
                $lineno = $record["lineno"];
                $isThat = null;

                if (empty($line)) {
                    continue;
                }

                $cmd = RivescriptType::fromCode(substr($script, 0, 1));
                $check = $this->checkSyntax($cmd, $line, $filename, $lineno);;

                if ($check->status == ParseResultStatus::ERROR) {
                    $this->_error($check->message);
                }

                if (str_starts_with($script, '//')) {
                    continue;
                }

                if (str_starts_with($script, '/*') || str_starts_with($script, '*/')
                    || str_starts_with($script, '#')) {
                    if (str_starts_with($script, '#')) {
                        $this->output(
                            RivescriptMessage::Warning(
                                "Using the # symbol for comments is deprecated in :filename line :lineno",
                                ['filename' => $filename, 'lineno' => $lineno]
                            )
                        );
                    }
                    continue;
                }

                if (isset($context->label) && ($context->label['type'] === 'code' || $context->label['type'] === 'begin')) {
                    $this->output(RivescriptMessage::Say("Check for unsupported code languages"));

                    if ($cmd !== RivescriptType::LABEL_CLOSE) {
                        $context->label['lines'][] = $script;
                        continue;
                    }
                }

                if ($cmd == RivescriptType::TRIGGER) {
                    $inThat = null;
                }

                for ($i = $step + 1; $i < count($lines); $i++) {
                    $lookline = trim($lines[$i]['script']);

                    if (strlen($lookline) < 2) {
                        continue;
                    }

                    $lookCmd = RivescriptType::fromCode(substr($lookline, 0, 1));
                    $lookahead = trim(substr($lookline, 1));

                    // TODO: Replace \s with space
                    $lookahead = preg_replace('/\s+/', ' ', $lookahead);

                    if (empty($lookahead)) {
                        continue;
                    }

                    # The lookahead command has to be either a % or a ^.
                    if ($lookCmd !== RivescriptType::PREVIOUS && $lookCmd !== RivescriptType::CONTINUE) {
                        break;
                    }

                    # If the current command is a +, see if the following is
                    # a %.
                    if ($cmd == RivescriptType::TRIGGER) {
                        if ($lookCmd == RivescriptType::PREVIOUS) {
                            $isThat = $lookahead;
                            break;
                        } else {
                            $isThat = null;
                        }
                    }

                    # If the current command is a ! and the next command(s) are
                    # ^, we'll tack each extension on as a line break (which is
                    # useful information for arrays).
                    if ($cmd === RivescriptType::DEFINITION) {
                        if ($lookCmd == RivescriptType::CONTINUE) {
                            $line .= '<crlf>'. $lookahead;
                            continue;
                        }
                    }

                    # If the current command is not a ^ and the line after is
                    # not a %, but the line after IS a ^, then tack it on to the
                    # end of the current line.
                    if ($cmd != RivescriptType::CONTINUE and $lookCmd != RivescriptType::PREVIOUS) {
                        if ($lookCmd == RivescriptType::CONTINUE) {
                            $this->output(RivescriptMessage::Warning("IMPLEMENT CONTINUE"));
//                            line += self . concat_modes . get(
//                                    local_options["concat"],
//                                    ""
//                                ) + lookahead
                        } else {
                            break;
                        }
                    }
                }

                switch ($cmd) {
                    case RivescriptType::LABEL_OPEN:

                        unset($context->label);

                        $this->output(RivescriptMessage::Say("Open label"));

                        $context->label = match (true) {
                            str_starts_with($line, 'object') => $this->createEmptyLabel("code"),
                            str_starts_with($line, 'topic') => $this->createEmptyLabel("topic"),
                            str_starts_with($line, 'begin') => $this->createEmptyLabel("begin"),
                            default => null,
                        };

                        if ($context->label['type'] == 'begin') {
                            $this->output(RivescriptMessage::Say("\tFound the BEGIN block."));
                        }

                        if ($context->label['type'] == "topic") {
                            $headline = trim(substr($line, 5));
                            [$name] = $fields = explode(" ", $headline);

                            $context->label['name'] = trim($name);

                            $this->output(RivescriptMessage::Say("\tSet topic to :topic", ["topic" => $name]));

                            $context->lastTopic = $context->topic;
                            $context->topic = $name;

                            $this->initTopic($name);

                            $mode = '';
                            if (count($fields) > 2) {
                                array_shift($fields);
                                foreach ($fields as $field) {
                                    if ($field == 'includes') {
                                        $mode = 'includes';
                                    } elseif ($field == 'inherits') {
                                        $mode = 'inherits';
                                    } elseif ($mode == 'includes') {
                                        $this->values['topics'][$context->topic]['includes'][$field] = 1;
                                    } elseif ($mode == 'inherits') {
                                        $this->values['topics'][$context->topic]['inherits'][$field] = 1;
                                    }
                                }
                            }
                        }

                        if ($context->label['type'] == "code") {
                            $headline = trim(substr($line, 6));
                            $lang = null;

                            if (str_contains($headline, " ")) {
                                [$name, $lang] = explode(" ", $headline);
                            } else {
                                $name = $headline;
                            }

                            if (!$lang) {
                                $lang = "php";
                                $this->output(
                                    RivescriptMessage::Warning(
                                        "Trying to parse unknown programming language in :filename on line :lineno",
                                        ["filename" => $filename, "lineno" => $lineno]
                                    )
                                );
                            }

                            $context->label['name'] = trim($name);
                            $context->label['language'] = strtolower(trim($lang));
                        }

                        break;
                    case RivescriptType::LABEL_CLOSE:
                        $key = match ($context->label['type']) {
                            'code' => 'objects',
                            'begin' => 'begin',
                            default => null,
                        };


                        if ($context->label['type'] == 'begin' || $context->label['type'] == 'topic') {
                            $this->output(RivescriptMessage::Say("\tEnd topic label."));
                            $context->topic = $context->lastTopic;
                        } elseif ($context->label['type'] == 'code') {
                            $this->output(RivescriptMessage::Say("\tEnd object label."));
                        }

                        if ($key) {
                            $this->values[$key][] = $context->label;
                        }

                        if (isset($context->lastTopic)) {
                            $context->topic = $context->lastTopic;
                        }

                        unset($context->lastTopic);
                        unset($context->label);

                        break;
                    case RivescriptType::DEFINITION:
                        $parsed = $this->parseDefinition($line, $filename, $lineno);

                        if ($parsed->status == ParseResultStatus::ERROR) {
                            $this->_error($parsed->message);
                        }

                        if ($parsed->status == ParseResultStatus::WARNING) {
                            $this->output($parsed->message);
                        }

                        if ($parsed->result) {
                            if ($parsed->result['type'] == "local") {
                                $name = $parsed->result['name'];
                                $value = $parsed->result['value'];
                                $local_options[$name] = $value;

                                $this->output(
                                    RivescriptMessage::Say(
                                        "\tSet parser option :name = :value",
                                        ['name' => $name, 'value' => $value]
                                    )
                                );
                                break;
                            }
                            if ($parsed->result['type'] == "global") {
                                $name = $parsed->result['name'];
                                $value = $parsed->result['value'];

                                if ($value == "<undef>") {
                                    $this->output(RivescriptMessage::Warning("\tUNDEF NOT SUPPORTED YET."));
                                }

                                if ($name == "debug") {
                                    if (strtolower($value) == 'true') {
                                        $value = true;
                                    } else {
                                        $value = false;
                                    }
                                } elseif ($name == "depth") {
                                    $value = (int)$value;
                                } elseif ($name == "strict") {
                                    if (strtolower($value) == 'true') {
                                        $value = true;
                                    } else {
                                        $value = false;
                                    }
                                }

                                $this->values["begin"]["global"] = $value;

                                $this->output(RivescriptMessage::Say("\tSTODO: Support undef"));
                                $this->output(
                                    RivescriptMessage::Say(
                                        "\tSet global option :name = :value",
                                        ['name' => $name, 'value' => $value]
                                    )
                                );
                                break;
                            }
                            if ($parsed->result['type'] !== "version") {
                                $type = $parsed->result['type'];
                                $value = $parsed->result['value'];
                                $name = $parsed->result['name'];

                                $this->values[$type][$name] = $value;

                                if ($value == "<undef>") {
                                    $this->output(RivescriptMessage::Warning("\tUNDEF NOT SUPPORTED YET."));
                                }

                                if ($type == "array") {
//                                    Did this have multiple parts?
//                                        parts = value.split("<crlf>")
                                    $this->output(
                                        RivescriptMessage::Warning("\tARRAY MULTIPLE PART NOT SUPPORTED YET")
                                    );
                                }

                                if (isset($this->values["begin"][$type])) {
                                    $this->values["begin"][$type][$name] = $value;
                                }

                                $this->output(
                                    RivescriptMessage::Say(
                                        "\tSet :type option :name = :value",
                                        ['type' => $type, 'name' => $name, 'value' => $value]
                                    )
                                );
                            }
                        }

                        unset($parsed);
                        break;
                    case RivescriptType::TRIGGER:

                        unset($context->trigger);

                        if ($this->forceCase) {
                            $script = strtolower($script);
                        }

                        $this->output(RivescriptMessage::Say("Trigger pattern: :script", ['script' => $script]));

                        if (!isset($this->values['topics'][$context->topic])) {
                            $this->output(
                                RivescriptMessage::Say("Adding topic :topic", ['topic' => $context->topic])
                            );
                            $this->initTopic($context->topic);
                        }

                        $context->trigger = [
                            "trigger" => $line,
                            "reply" => [],
                            "condition" => [],
                            "redirect" => null,
                            "previous" => $isThat,

                        ];

                        $this->values["topics"][$context->topic]["syntax"][$line] = [
                            "previous" => $isThat,
                            "filename" => $filename,
                            "lineno" => $lineno,
                        ];

                        $this->values["topics"][$context->topic]["triggers"][] = &$context->trigger;
                        break;
                    case RivescriptType::RESPONSE:
                        if ($context->trigger === null) {
                            $this->output(
                                RivescriptMessage::Warning(
                                    "Response found before trigger in :filename on line :lineno",
                                    ['filename' => $filename, 'lineno' => $lineno]
                                )
                            );
                        }

                        $context->trigger['reply'][] = $line;
//                        array_push($context->trigger['reply'], $script);
                        $this->output(RivescriptMessage::Say("Response: :script", ['script' => $script]));
                        break;
                    case RivescriptType::CONDITION:

                        if ($context->trigger === null) {
                            $this->output(
                                RivescriptMessage::Warning(
                                    "Response found before trigger in :filename on line :lineno",
                                    ['filename' => $filename, 'lineno' => $lineno]
                                )
                            );
                        }

                        if ($context->trigger['redirect']) {
                            $this->output(
                                RivescriptMessage::Warning(
                                    "You can't mix @Redirects with *Conditions in :filename on line :lineno",
                                    ['filename' => $filename, 'lineno' => $lineno]
                                )
                            );
                        }

                        $this->output(RivescriptMessage::Say("Condition: :script", ['script' => $script]));
                        $context->trigger['condition'][] = Str::strip($line);
                        break;
                    case RivescriptType::PREVIOUS: // TODO: Continue
                    case RivescriptType::CONTINUE:
                        // This was handled above

                        $this->output(
                            RivescriptMessage::Warning(
                                "Previous or continue are NOT handled in :script",
                                ['script' => $script]
                            )
                        );
                        break;
                    case RivescriptType::REDIRECT:

                        if ($context->trigger === null) {
                            $this->output(
                                RivescriptMessage::Warning(
                                    "Response found before trigger in :filename on line :lineno",
                                    ['filename' => $filename, 'lineno' => $lineno]
                                )
                            );
                        }

                        if (count($context->trigger['reply']) > 0 || count($context->trigger['condition']) > 0) {
                            $this->output(
                                RivescriptMessage::Say(
                                    "You can't mix @Redirects with -Replies or *Conditions in :filename on line :lineno",
                                    ['filename' => $filename, 'lineno' => $lineno]
                                )
                            );
                        }

                        $this->output(
                            RivescriptMessage::Say("Redirect response to: :script", ['script' => $script])
                        );
                        $context->trigger['redirect'][] = Str::strip($line);
                        break;
                    default:
                        $this->output(
                            RivescriptMessage::Warning("Found unknown command :script", ['script' => $script])
                        );
                        break;
                }
            }
        }

        return $this->values;
    }

    /**
     * @param string $line The line to parse.
     * @param string $filename The filename the code is from.
     * @param int    $lineno The line number.
     *
     * @return \Axiom\Rivescript\Parser\ParseResult
     */
    private function parseDefinition(string $line, string $filename, int $lineno): ParseResult
    {
        $halves = explode('=', $line, 2);
        $left = explode(' ', Str::strip($halves[0])) ?? [];
        $value = "";
        $name = "";
        $type = "";

        if (count($halves) == 2) {
            $value = Str::strip($halves[1]);
        }

        if (count($left) >= 1) {
            $type = Str::strip($left[0]);
            if (count($left) >= 2) {
                array_shift($left);
                $name = Str::strip(implode(" ", $left));
            }
        }

        if ($type == "version") {
            $value = (float)$value;
            $result = [
                'type' => $type,
                'value' => $value,
            ];

            if ($value < self::RS_VERSION) {
                return ParseResult::withError(
                    "Lower preferred version RiveScript version as expected. We only support :version at :filename line :lineno",
                    ['filename' => $filename, 'lineno' => $lineno, 'version' => self::RS_VERSION]
                );
            }

            if ($value > (float)self::RS_VERSION) {
                return ParseResult::withWarning(
                    "Higher preferred version RiveScript version as expected. This parser supports version :version at :filename line :lineno",
                    ['filename' => $filename, 'lineno' => $lineno, 'version' => self::RS_VERSION],
                    $result
                );
            }

            return ParseResult::with($result);
        }

        if ($type !== "array") {
            $value = str_replace("\r\n", "", $value);
        }

        if (strlen($name) === 0) {
            return ParseResult::withError(
                "Undefined variable name at :filename line :lineno",
                ['filename' => $filename, 'lineno' => $lineno]
            );
        }

        if (strlen($value) == 0) {
            return ParseResult::withError(
                "Undefined variable value at :filename line :lineno",
                ['filename' => $filename, 'lineno' => $lineno]
            );
        }

        return ParseResult::with([
            'type' => $type,
            'name' => $name,
            'value' => $value,
        ]);
    }

    /**
     * Check the line for syntax errors.
     *
     * @param \Axiom\Rivescript\RivescriptType $type
     * @param string                           $line The line to check.
     * @param string                           $filename
     * @param int                              $lineno
     *
     * @return ParseResult
     */
    protected function checkSyntax(RivescriptType $type, string $line, string $filename, int $lineno): ParseResult
    {
        if ($type == RivescriptType::DEFINITION) {
            # ! Definition
            #   - Must be formatted like this:
            #     ! type name = value
            #     OR
            #     ! type = value
            #   - Type options are NOT enforceable, for future compatibility; if RiveScript
            #     encounters a new type that it can't handle, it can safely warn and skip it.
            if (!$this->matchesPattern("/^.+(?:\s+.+|)\s*=\s*.+?$/", $line)) {
                return ParseResult::withError(
                    "Invalid format for !Definition line: must be '! type name = value' OR '! type = value'",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            } elseif ($this->matchesPattern('/^array/', $line)) {
                if ($this->matchesPattern("/\=\s?\||\|\s?$/", $line)) {
                    return ParseResult::withError(
                        "Piped arrays can't begin or end with a |",
                        ['filename' => $filename, 'lineno' => $lineno]
                    );
                } elseif ($this->matchesPattern("/\|\|/", $line)) {
                    return ParseResult::withError(
                        "Piped arrays can't contain blank entries",
                        ['filename' => $filename, 'lineno' => $lineno]
                    );
                }
            }
        } elseif ($type === RivescriptType::LABEL_OPEN) {
            // > Label
            // - The "begin" label must have only one argument ("begin")
            // - The "topic" label must be lowercased but can inherit other topics
            // - The "object" label must follow the same rules as "topic", but don't
            //   need to be lowercased.
            # > Label
            #   - The "begin" label must have only one argument ("begin")
            #   - "topic" labels must be lowercase but can inherit other topics ([A-Za-z0-9_\s])
            #   - "object" labels follow the same rules as "topic" labels, but don't need be lowercase
            if ($this->matchesPattern("/^begin/", $line)
                && !$this->matchesPattern("/^begin$/", $line)) {
                return ParseResult::withError(
                    "The 'begin' label takes no additional arguments, should be verbatim '> begin'",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            } elseif ($this->matchesPattern("/^topic/", $line)
                && $this->matchesPattern("/[^a-z0-9_\-\s]/", $line)) {
                return ParseResult::withError(
                    "Topics should be lowercased and contain only numbers and letters!",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            } elseif ($this->matchesPattern("/^object/", $line)
                && $this->matchesPattern("/[^a-z0-9_\-\s]/", $line)) {
                return ParseResult::withError(
                    "Objects can only contain numbers and lowercase letters!",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }
        } elseif ($type === RivescriptType::TRIGGER || $type === RivescriptType::PREVIOUS || $type === RivescriptType::REDIRECT) {
            # + Trigger, % Previous, @ Redirect
            #   This one is strict. The triggers are to be run through Perl's regular expression
            #   engine. Therefore, it should be acceptable by the regexp engine.
            #   - Entirely lowercase
            #   - No symbols except: ( | ) [ ] * _ # @ { } < > =
            #   - All brackets should be matched

            if ($this->utf8 === true) {
                if ($this->matchesPattern("/[A-Z\\.]/", $line)) {
                    return ParseResult::withError(
                        "Triggers can't contain uppercase letters or dots in UTF-8 mode.",
                        ['filename' => $filename, 'lineno' => $lineno]
                    );
                }
            } elseif ($this->matchesPattern("/[^a-z0-9(\|)\[\]*_#\@{}<>=\s]/", $line) === true) {
                return ParseResult::withError(
                    "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > =",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }

            $parens = 0; # Open parenthesis
            $square = 0; # Open square brackets
            $curly = 0; # Open curly brackets
            $chevron = 0; # Open angled brackets
            $len = strlen($line);

            for ($i = 0; $i < $len; $i++) {
                $chr = $line[$i];

                # Count brackets.
                if ($chr === '(') {
                    $parens++;
                }
                if ($chr === ')') {
                    $parens--;
                }
                if ($chr === '[') {
                    $square++;
                }
                if ($chr === ']') {
                    $square--;
                }
                if ($chr === '{') {
                    $curly++;
                }
                if ($chr === '}') {
                    $curly--;
                }
                if ($chr === '<') {
                    $chevron++;
                }
                if ($chr === '>') {
                    $chevron--;
                }
            }

            if ($parens) {
                return ParseResult::withError(
                    "Unmatched " . ($parens > 0 ? "left" : "right") . " parenthesis bracket ()",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }
            if ($square) {
                return ParseResult::withError(
                    "Unmatched " . ($square > 0 ? "left" : "right") . " square bracket []",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }
            if ($curly) {
                return ParseResult::withError(
                    "Unmatched " . ($curly > 0 ? "left" : "right") . " curly bracket {}",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }
            if ($chevron) {
                return ParseResult::withError(
                    "Unmatched " . ($chevron > 0 ? "left" : "right") . " angled bracket <>",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }
        } elseif ($type === RivescriptType::RESPONSE || $type === RivescriptType::CONTINUE || $type === RivescriptType::COMMENT) {
            # - Trigger, ^ Continue, / Comment
            # These commands take verbatim arguments, so their syntax is loose.
        } elseif ($type === RivescriptType::CONDITION) {
            # * Condition
            #   Syntax for a conditional is as follows:
            #   * value symbol value => response
            if (!$this->matchesPattern("/.+?\s(==|eq|!=|ne|<>|<|<=|>|>=)\s.+?=>.+?$/", $line)) {
                return ParseResult::withError(
                    "Invalid format for !Condition: should be like `* value symbol value => response`",
                    ['filename' => $filename, 'lineno' => $lineno]
                );
            }
        }

        return ParseResult::ok();
    }
}