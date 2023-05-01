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

use Axiom\Rivescript\Exceptions\Parser\ParserException;
use Axiom\Rivescript\Messages\MessageType;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\RivescriptCommand;
use Axiom\Rivescript\RivescriptEvent;
use Axiom\Rivescript\Traits\EventEmitter;
use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Utils\Str;

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
class Parser
{
    use EventEmitter;
    use Regex;

    /**
     * The supported Rivescript version
     */
    public const RS_VERSION = 2.0;


    /**
     * Container for the parsed values.
     *
     * @var array
     */
    protected array $values = [];

    public bool $forceCase = false;

    /**
     * Parser constructor.
     *
     * @param \Axiom\Rivescript\Rivescript $master A reference to the parent RiveScript instance.
     */
    public function __construct(
        protected readonly Rivescript $master,
    ) {
        $this->forceCase = true;
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
                "version" => self::RS_VERSION,
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
     * @param string $message The message to output.
     *
     * @return void
     */
    private function say(string $message): void
    {
        $this->master->say($message . "\n");
    }

    /**
     * Output a warning.
     *
     * @param string $message  The message to display.
     * @param string $filename The filename the code is from.
     * @param int    $lineno   The line number the warning is referring to.
     *
     * @return void
     */
    private function warn(string $message, string $filename, int $lineno): void
    {
        $this->master->warn($message . "\n", [
                'filename' => $filename,
                'lineno' => $lineno
            ]
        );
    }


    /**
     * Output a error.
     *
     * @param string $message  The message to display.
     * @param string $filename The filename the code is from.
     * @param int    $lineno   The line number the warning is referring to.
     *
     * @return void
     */
    private function error(string $message, string $filename, int $lineno): void
    {
        $this->master->error($message . "\n", [
                'filename' => $filename,
                'lineno' => $lineno
            ]
        );
    }

    /**
     * @throws \Axiom\Rivescript\Exceptions\Parser\ParserException
     */
    private function _error(RivescriptMessage $error)
    {
        $this->emit(RivescriptEvent::OUTPUT, $error);

        throw new ParserException("Error while parsing script.");
    }

    private function _warn(RivescriptMessage $warning)
    {
        $this->emit(RivescriptEvent::OUTPUT, $warning);
    }

    /**
     * Parse the RiveScript code.
     *
     * @param string $code The code in string.
     *
     * @throws \Axiom\Rivescript\Exceptions\Parser\ParserException
     * @return array<string, mixed>
     */
    public function parse(string $filename, string $code): array
    {
        $this->reset();

        $topic = "random";

        $lines = explode("\n", $code);
        $lineno = 0;

        if (is_array($lines) && count($lines) > 0) {
            $original = $lines;
            $filtered = [];

            /**
             * First filter out the comments and objects.
             */
            // TODO: Syntax check the code in this first loop.
            for ($i = 0; $i < count($original); $i++) {
                $rawLine = trim($original[$i]);
                $line = trim(substr($rawLine, 1));
                $cmd = RivescriptCommand::fromCode(substr($rawLine, 0, 1));

                if (empty($line)) {
                    continue;
                }

                if (str_starts_with($line, '/*') || str_starts_with($line, '*/')
                    || str_starts_with($line, '#')) {
                    if (str_starts_with($line, '#')) {
                        $this->warn("Using the # symbol for comments is deprecated", $filename, $lineno);
                    }
                    continue;
                }


                if ($cmd == RivescriptCommand::LABEL_OPEN && !isset($label)) {
                    $label = match (true) {
                        str_starts_with($line, 'object') => $this->createEmptyLabel("code"),
                        str_starts_with($line, 'topic') => $this->createEmptyLabel("topic"),
                        str_starts_with($line, 'begin') => $this->createEmptyLabel("begin"),
                        default => null,
                    };
                }

                if (isset($label) && is_array($label)) {
                    if ($cmd == RivescriptCommand::LABEL_OPEN) {
                        switch ($label['type']) {
                            case "code":
                                $headline = trim(substr($line, 6));
                                [$language, $name] = explode(" ", $headline);

                                $label['name'] = trim($name);
                                $label['language'] = trim($language);
                                $label['valid'] = true;
                                break;
                            case "topic":
                                $headline = trim(substr($line, 5));
                                [$name] = explode(" ", $headline);

                                $label['name'] = trim($name);
                                $label['valid'] = true;
                                break;
                            case "begin":
                                $label['valid'] = true;
                                break;
                        }

                        if (!$label['valid']) {
                            unset($label);
                        }
                    } else {
                        $valuesKey = match ($label["type"]) {
                            "topic" => "topics",
                            "begin" => "begin_fixme",
                            "code" => "objects",
                        };

                        if ($cmd == RivescriptCommand::LABEL_CLOSE) {
                            $this->values[$valuesKey][] = $label;
                            unset($label);
                        } else {
                            $label["lines"][] = $rawLine;
                        }
                    }
                } else {
                    $filtered[] = $rawLine;
                }
            }

            $lineno = 10;

            /**
             * Work on the filtered lines.
             */
            foreach ($filtered as $line) {
                $rawLine = trim($line);
                $line = trim(substr($rawLine, 1));


                $cmd = RivescriptCommand::fromCode(substr($rawLine, 0, 1));

                $syntax = $this->checkSyntax($cmd->value, $line, $filename, $lineno);;

                if ($syntax->status == ParseResultStatus::ERROR) {
                    $this->_error($syntax->message);
                }


                switch ($cmd) {
                    case RivescriptCommand::DEFINITION:
                        $parsed = $this->parseDefinition($line, $filename, $lineno);

                        if ($parsed->status == ParseResultStatus::ERROR) {
                            $this->_error($parsed->message);
                        }

                        if ($parsed->status == ParseResultStatus::WARNING) {
                            $this->_warn($parsed->message);
                        }

                        if ($parsed->result) {
                            if ($parsed->result['type'] !== "version") {
                                $type = $parsed->result['type'];
                                $value = $parsed->result['value'];
                                $name = $parsed->result['name'];

                                $this->values[$type][$name] = $value;

                                if (isset($this->values["begin"][$type])) {
                                    $this->values["begin"][$type][$name] = $value;
                                }
                            }
                        }

                        unset($parsed);
                        break;

                    case RivescriptCommand::TRIGGER:
                        echo "Found trigger {$rawLine}\n";
                        break;

                    default:
                        echo "Found unknown command {$rawLine}\n";
                        break;
                }
            }
        }

//        print_r($filtered);
//
//
        print_r($this->values);

        return $this->values;
    }


    /**
     * @param $line
     * @param $filename
     * @param $lineno
     *
     * @return \Axiom\Rivescript\Parser\ParseResult
     */
    private function parseDefinition($line, $filename, $lineno): ParseResult
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
     * Create an empty object label.
     *
     * @param string $type The type of object to create.
     *
     * @return array|null
     */
    private function createEmptyLabel(string $type): ?array
    {
        return match ($type) {
            "code" => ["type" => "code", "valid" => false, "name" => "", "language" => "", "lines" => []],
            "topic" => ["type" => "topic", "valid" => false, "lines" => []],
            "begin" => ["type" => "begin", "valid" => false, "lines" => []],
            default => null,
        };
    }

    /**
     * Check the line for syntax errors.
     *
     * @param string $cmd  The command type character.
     * @param string $line The line to check.
     *
     * @return ParseResult
     */
    protected function checkSyntax(string $cmd, string $line, string $filename, int $lineno): ParseResult
    {
        if ($cmd === '!') {
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
        } elseif ($cmd === '>') {
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
        } elseif ($cmd === '+' || $cmd === '%' || $cmd === '@') {
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
        } elseif ($cmd === '-' || $cmd === '^' || $cmd === '/') {
            # - Trigger, ^ Continue, / Comment
            # These commands take verbatim arguments, so their syntax is loose.
        } elseif ($cmd === '*') {
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

        return ParseResult::withSuccess([]);
    }
}