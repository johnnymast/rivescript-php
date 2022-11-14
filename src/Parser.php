<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript;

use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Utils\Utils;

/**
 * Parser class
 *
 * This is a port of the rivescript-js parser written by Noah Petherbridge.
 * The original source code can be found here:
 *
 * https://github.com/aichaos/rivescript-js/blob/423cd4ac41ecc4f837639ee8bc8d2163bf61860b/src/parser.js
 *
 * PHP version 7.4 and higher.
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
    use Regex;

    /**
     *
     */
    public const RS_VERSION = "2.0";

    /**
     * @var bool
     */
    protected bool $strict = true;
    /**
     * @var bool
     */
    protected bool $utf8 = true;

    /**
     * @var bool
     */
    protected bool $forceCase = false;

    /**
     * @var string
     */
    protected string $concat = "none";

    /**
     * @param \Axiom\Rivescript\Rivescript $master
     */
    public function __construct(
        protected Rivescript $master
    )
    {
        $this->strict = $this->master->strict;
        $this->utf8 = $this->master->utf8;
        $this->forceCase = $this->master->forceCase;
        $this->concat = $this->master->concat;
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
        $this->master->warn($message . "\n", $filename, $lineno);
    }

    /**
     * @param string        $filename The filename for the code.
     * @param string        $code     The code in string.
     * @param callable|null $onError  The error callback.
     *
     * @return array<string, mixed>
     */
    public function parse(string $filename, string $code, callable $onError = null): array
    {

        $ast = [
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

        if (!$onError) {
            $onError = fn($err, $filename, $lineno) => $this->warn($err, $filename, $lineno);
        }

        $topic = "random";
        $comment = false;
        $inobj = false;
        $objName = "";
        $objLang = "";
        $objBuf = [];
        $lastcmd = "";
        $curTrig = [];

        $inThat = false;
        $curTrigIdx = 0;

        // FIXME: check for rivescript class concat options
        $localOptions = [
            "concat" => "none"
        ];

        $concatModes = [
            "none" => "",
            "newline" => "\n",
            "space" => " "
        ];

        $lines = explode("\n", $code);
        for ($lp = 0, $len = count($lines); $lp < $len; $lp++) {
            $line = Utils::strip($lines[$lp]);
            $lineno = ($lp + 1);

            if (strlen($line) === 0) {
                continue;
            }


            //-----------------------------
            // Are we inside an `> object`?
            //-----------------------------
            if ($inobj) {
                // End of the object?
                if (strpos($line, "< object") > -1 || strpos($line, "<object") > -1) { // TODO
                    // End the object.
                    if (strlen($objName) > 0) {
                        $ast['objects'][] = [
                            "name" => $objName,
                            "language" => $objLang,
                            "code" => $objBuf
                        ];
                    }
                    $objName = $objLang = "";
                    $objBuf = [];
                    $inobj = false;
                } else {
                    $objBuf[] = $line;
                }
                continue;
            }


            // Skip comments
            if (str_starts_with($line, '//')) {
                continue;
            } else if (str_starts_with($line, '#')) {
                $this->warn("Using the # symbol for comments is deprecated", $filename, $lineno);
                continue;
            } else if (str_starts_with($line, '/*')) {
                if (str_contains($line, '*/')) {
                    continue;
                }

                $comment = true;
            } else if (str_starts_with($line, '*/')) {
                $comment = false;
                continue;
            }

            if ($comment) {
                continue;
            }

            if ($len < 2) {
                $this->warn("Weird single-character line '{$line}' found (in topic {$topic})", $filename, $lineno);
                continue;
            }

            $cmd = substr($line, 0, 1);
            $line = trim(substr($line, 1));

            // Ignore in-line comments if there's a space before and after the "//"
            if (($pos = strpos($line, " //")) > -1) {
                $line = substr($line, 0, $pos);
            }

            //https://github.com/aichaos/rivescript-js/blob/master/src/parser.js#L200


            // Allow the ?Keyword command to work around UTF-8 bugs for users who
            // wanted to use `+ [*] keyword [*]` with Unicode symbols that don't match
            // properly with the usual "optional wildcard" syntax.
//            if (cmd === "?") {
//                // The ?Keyword command is really an alias to +Trigger with some workarounds
//                // to make it match the keyword _anywhere_, in every variation so it works
//                // with Unicode strings.
//                let variants = [
//                    line,
//                    `[*]${line}[*]`,
//                    `*${line}*`,
//                    `[*]${line}*`,
//                    `*${line}[*]`,
//                    `${line}*`,
//                    `*${line}`
//                ];
//				cmd = "+";
//				line = "(" + variants.join("|") + ")";
//				self.say(`Rewrote ?Keyword as +Trigger: ${line}`);
//			}

            if ($this->forceCase && $cmd == '+') {
                $line = strtolower($line);
            }

            // test strict and warn ...

            $syntaxError = $this->checkSyntax($cmd, $line);
            if (!empty($syntaxError)) {
                if ($this->strict) {
                    call_user_func($onError, "Syntax error: {$syntaxError} at {$filename} line {$lineno} near {$cmd} {$line}");
                } else {
                    $this->warn("Syntax error: {$syntaxError} at {$filename} line {$lineno} near {$cmd} {$line} (in topic {$topic})", $filename, $lineno);
                }
            }

            if ($cmd === '+') {
                $inThat = null;
            }

            $this->say("Cmd: {$cmd}; line: {$lineno}");

            for ($li = $lp+1, $len2 = count($lines); $li < $len2; $li++) {
                $lookahead = $lines[$li];
                $lookahead = Utils::strip($lookahead);

                if (strlen($lookahead) < 2) {
                    continue;
                }

                $lookCmd = substr($lookahead, 0,);
                $lookahead = Utils::strip(substr($lookahead, 1));

                // We only care about a couple lookahead command types.
                if ($lookCmd !== "%" && $lookCmd !== "^") {
                    break;
                }

                // Only continue if the lookahead has any data.
                if (strlen($lookahead) === 0) {
                    break;
                }

                $this->say(`\tLookahead {$li}: {$lookCmd} {$lookahead}`);

                // If the current command is a +, see if the following is a %.
                if ($cmd === "+") {
                    if ($lookCmd === "%") {
                        $isThat = $lookahead;
                        break;
                    } else {
                        $isThat = null;
                    }
                }

                // If the current command is a ! and the next command(s) are ^ we'll
                // tack each extension on as a line break (which is useful information
                // for arrays).
                if ($cmd === "!") {
                    if ($lookCmd === "^") {
                        $line += "\r\n{$lookahead}";
                    }
                    continue;
                }

                // If the current command is not a ^, and the line after is not a %,
                // but the line after IS a ^, then tack it on to the end of the current
                // line.
                if ($cmd !== "^" && $lookCmd !== "%") {
                    if ($lookCmd === "^") {
                        // Which character to concatenate with?
                        if (isset($concatModes[$localOptions['concat']])) {
                            $line += $concatModes[$localOptions['concat']] + $lookahead;
                        } else {
                            $line += $lookahead;
                        }
					} else {
                        break;
                    }
                }
            }

            $type = "";
            $name = "";
            switch ($cmd) {
                case '!':
                    $halves = explode('=', $line, 2);
                    $left = (array)explode(' ', Utils::strip($halves[0]));
                    $value = "";
                    $name = "";
                    $type = "";

                    if (count($halves) == 2) {
                        $value = Utils::strip($halves[1]);
                    }

                    if (count($left) >= 1) {
                        $type = Utils::strip($left[0]);
                        if (count($left) >= 2) {
                            array_shift($left);
                            $name = Utils::strip(implode(" ", $left));
                        }
                    }

                    if ($type !== "array") {
                        $value = str_replace("\r\n", "", $value);
                    }

                    if ($type === "version") {
                        if ((float)$value > (float)self::RS_VERSION) {
                            $version = self::RS_VERSION;
                            call_user_func($onError, "Unsupported RiveScript version. We only support {$version} at {$filename} line {$lineno}");
                            return $ast;
                        }
                        break;
                    }

                    if (strlen($name) === 0) {
                        $this->warn("Undefined variable name", $filename, $lineno);
                        break;
                    }

                    if (strlen($value) == 0) {
                        $this->warn("Undefined variable value", $filename, $lineno);
                        break;
                    }

                    switch ($type) {
                        case 'local':
                            $this->say("\tSet local parser option  {$name} = {$value}");
                            $localOptions[$name] = $value;
                            break;
                        case 'global':
                            $this->say("\tSet global  {$name} = {$value}");
                            $ast['begin']['global'][$name] = $value;
                            break;
                        case 'var':
                            $this->say("\tSet bot variable {$name} = {$value}");
                            $ast['begin']['var'][$name] = $value;
                            break;
                        case 'array':
                            if ($value === "<undef>") {
                                $ast['begin']['array'][$name] = "<undef>";
                                break;
                            }

                            $parts = explode("\r\n", $value);
                            $fields = [];

                            foreach ($parts as $part) {
                                if (strpos($part, '|') > -1) {
                                    $val = explode("|", $part);
                                } else {
                                    $val = explode(" ", $part);
                                }

                                $val = preg_replace("/\\s/", " ", $val);
                                $fields = array_merge($val, $fields);
                            }

                            $fields = array_filter($fields, fn($field) => $field !== "");

                            $json = json_encode($fields);
                            $this->say("\tSet array {$name} = {$json}");
                            $ast['begin']["array"][$name] = $fields;
                            break;
                        case 'sub':
                            $this->say("\tSet substitution {$name} = {$value}");
                            $ast['begin']['sub'][$name] = $value;
                            break;
                        case 'person':
                            $this->say("\tSet person substitution {$name} = {$value}");
                            $ast['begin']['person'][$name] = $value;
                            break;
                        default:
                            $this->warn("Unknown definition type {$type}", $filename, $lineno);
                            break;
                    }
                    break;

                case '>':
                    $temp = explode(" ", Utils::strip($line));
                    $name = "";
                    $fields = [];

                    $type = array_shift($temp);
                    $name = array_shift($temp);
                    $fields = $temp;

                    switch ($type) {
                        case "begin":
                        case "topic":
                            if ($type == "begin") {
                                $this->say("Found the BEGIN block.");
                                $type = "topic";
                                $name = "__begin__";
                            }

                            if ($this->forceCase) {
                                $name = strtolower($name);
                            }

                            $this->say("Set topic to {$name}");
                            $curTrig = null;
                            $topic = $name;

                            if (isset($ast['topics'][$topic]) === false) {
                                $ast['topics'][$topic] = [
                                    "includes" => [],
                                    "inherits" => [],
                                    "triggers" => [],
                                ];
                            }

                            $mode = "";
                            foreach ($fields as $field) {
                                if ($field === "includes" || $field === "inherits") {
                                    $mode = $field;
                                } else if (!empty($mode)) {
                                    $ast["topics"][$topic][$field] = 1;
                                }
                            }
                            break;
                        case "object":
                            $lang = "";

                            if (count($fields) > 0) {
                                $lang = strtolower($fields[0]);
                            }

                            if (empty($lang)) {
                                $this->warn("Trying to parse unknown programming language", $filename, $lineno);
                                $lang = "php";
                            }

                            $objName = $name;
                            $objLang = $lang;
                            $objBuf = [];
                            $inobj = true;
                            break;

                        default:
                            $this->warn("Unknown label type {$type}", $filename, $lineno);
                    }

                    break;

                case '<':
                    $type = $line;
                    if ($type === "begin" || $type === "topic") {
                        $this->say("\tEnd the topic label.");
                        $topic = "random";
                    } else if ($type === "object") {
                        $this->say("\tEnd the object label.");
                        $inobj = false;
                    }
                    break;

                case '+':

                    $this->say("\tTrigger pattern: {$line}");

                    if (!isset($ast['topics'][$topic])) {
                        $ast['topics'][$topic] = [
                            "includes" => [],
                            "inherits" => [],
                            "triggers" => [],
                        ];
                    }

                    $curTrig = [
                        "trigger" => $line,
                        "reply" => [],
                        "condition" => [],
                        "redirect" => null,
                        "previous" => $inThat
                    ];

                    $ast['topics'][$topic]['triggers'][] = $curTrig;
                    $curTrigIdx = count($ast['topics'][$topic]['triggers']) - 1;
                    break;

                case '-':

                    if ($curTrig === null) {
                        $this->warn("Response found before trigger", $filename, $lineno);
                        break;
                    }

                    $this->say("\tResponse: {$line}");
                    $ast['topics'][$topic]['triggers'][$curTrigIdx]['reply'][] = $line;
                    break;

                case '*':
                    if ($curTrig === null) {
                        $this->warn("Condition found before trigger", $filename, $lineno);
                        break;
                    }

                    if ($ast['topics'][$topic]['triggers'][$curTrigIdx]['redirect']) {
                        $this->warn("You can't mix @Redirects with *Conditions", $filename, $lineno);
                    }

                    $this->say("\tCondition: {$line}");
                    $ast['topics'][$topic]['triggers'][$curTrigIdx]['condition'] = Utils::strip($line);
                    break;

                case '%':
                case '^':
                    // This was handled above
                    break;

                case '@':
                    if (count($curTrig['reply']) > 0 || count($curTrig['condition']) > 0) {
                        $this->warn("You can't mix @Redirects with -Replies or *Conditions", $filename, $lineno);
                    }

                    $this->say("\tRedirect response to: {$line}");
                    $ast['topics'][$topic]['triggers'][$curTrigIdx]['redirect'] = Utils::strip($line);
                    break;

                default:
                    $this->warn("Unknown command '{$cmd}' (in topic {$topic})", $filename, $lineno);
            }

        }
        print_r($code);

        return $ast;
    }

    /**
     * Check the line for syntax errors.
     *
     * @param string $cmd  The command type character.
     * @param string $line The line to check.
     *
     * @return string
     */
    protected function checkSyntax(string $cmd, string $line): string
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
                return "Invalid format for !Definition line: must be '! type name = value' OR '! type = value'";
            } else if ($this->matchesPattern('/^array/', $line)) {

                if ($this->matchesPattern("/\=\s?\||\|\s?$/", $line)) {
                    return "Piped arrays can't begin or end with a |";
                } else if ($this->matchesPattern("/\|\|/", $line)) {
                    return "Piped arrays can't include blank entries";
                }
            }
        } else if ($cmd === '>') {
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
                return "The 'begin' label takes no additional arguments, should be verbatim '> begin'";
            } elseif ($this->matchesPattern("/^topic/", $line)
                && $this->matchesPattern("/[^a-z0-9_\-\s]/", $line)) {
                return "Topics should be lowercased and contain only numbers and letters!";
            } elseif ($this->matchesPattern("/^object/", $line)
                && $this->matchesPattern("/[^a-z0-9_\-\s]/", $line)) {
                return "Objects can only contain numbers and lowercase letters!";
            }
        } else if ($cmd === '+' || $cmd === '%' || $cmd === '@') {

            # + Trigger, % Previous, @ Redirect
            #   This one is strict. The triggers are to be run through Perl's regular expression
            #   engine. Therefore, it should be acceptable by the regexp engine.
            #   - Entirely lowercase
            #   - No symbols except: ( | ) [ ] * _ # @ { } < > =
            #   - All brackets should be matched

            if ($this->utf8 === true) {
                if ($this->matchesPattern("/[A-Z\\.]/", $line)) {
                    return "Triggers can't contain uppercase letters, backslashes or dots in UTF-8 mode.";
                }
            } elseif ($this->matchesPattern("/[^a-z0-9(\|)\[\]*_#\@{}<>=\s]/", $line) === true) {
                return "Triggers may only contain lowercase letters, numbers, and these symbols: ( | ) [ ] * _ # @ { } < > =";
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
                return "Unmatched " . ($parens > 0 ? "left" : "right") . " parenthesis bracket ()";
            }
            if ($square) {
                return "Unmatched " . ($square > 0 ? "left" : "right") . " square bracket []";
            }
            if ($curly) {
                return "Unmatched " . ($curly > 0 ? "left" : "right") . " curly bracket {}";
            }
            if ($chevron) {
                return "Unmatched " . ($chevron > 0 ? "left" : "right") . " angled bracket <>";
            }
        } else if ($cmd === '-' || $cmd === '^' || $cmd === '/') {
            # - Trigger, ^ Continue, / Comment
            # These commands take verbatim arguments, so their syntax is loose.
        } else if ($cmd === '*') {
            # * Condition
            #   Syntax for a conditional is as follows:
            #   * value symbol value => response
            if (!$this->matchesPattern("/.+?\s(==|eq|!=|ne|<>|<|<=|>|>=)\s.+?=>.+?$/", $line)) {
                return "Invalid format for !Condition: should be like `* value symbol value => response`";
            }
        }

        return "";
    }
}


class Master
{
    public bool $forceCase = true;
    public bool $strict = true;
    public bool $utf8 = false;

    public string $concat = "none";

    public function warn(string $message, string $filename, int $lineno): void
    {
        $this->say("{$filename}:{$lineno} Error: {$message}\n");
    }

    public function say(string $message): void
    {
        echo $message;
    }
}

