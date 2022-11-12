<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Hoa\Math\Util;

require '../vendor/autoload.php';

//use Axiom\Rivescript\Utils\Misc;

class Parser
{
    public const RS_VERSION = "2.0";

    /**
     * @var array <string, mixed>
     */
    protected array $structure = [
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

    protected bool $strict = true;
    protected bool $utf8 = true;

    public function __construct(
        protected Master $master
    )
    {
        $this->strict = $this->master->strict;
        $this->utf8 = $this->master->utf8;
    }


    public function parse(string $filename, string $code, callable $onError = null)
    {

        if (!$onError) {
            $onError = fn($err, $filename, $lineno) => $this->warn($err, $filename, $lineno);
        }

        $topic = "random";
        $comment = false;
        $inobj = false;
        $objName = "";
        $objLang = "";
        $objBuf = [];
        $curTrig = [];
        $lastcmd = [];
        $inThat = false;

        // FIXME: check for rivescript class concat options
        $localOptions = [
            "contact" => "none"
        ];

        $concatModes = [
            "none" => "",
            "newline" => "\n",
            "space" => " "
        ];

        $lines = explode("\n", $code);
        foreach ($lines as $index => $line) {
            $lineno = ($index + 1);
            $line = Utils::strip($line);
            $len = strlen($line);

            if ($len === 0) {
                continue;
            }


            //-----------------------------
            // Are we inside an `> object`?
            //-----------------------------
//            if (inobj) {
//                // End of the object?
//                if (line.indexOf("< object") > -1 || line.indexOf("<object") > -1) { // TODO
//                    // End the object.
//                    if (objName.length > 0) {
//                        ast.objects.push({
//							name: objName,
//							language: objLang,
//							code: objBuf
//						});
//					}
//                    objName = objLang = "";
//                    objBuf = [];
//                    inobj = false;
//                } else {
//                    objBuf.push(line);
//                }
//                continue;
//            }
//

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
                echo '>' . $line . '<';
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


            if ($this->master->forceCase && $cmd == '+') {
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

            $this->say("Cmd: {$cmd}; line: {$lineno}\n");
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
                            return $this->structure;
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
                            $this->structure['begin']['global'][$name] = $value;
                            break;
                        case 'var':
                            $this->say("\tSet bot variable {$name} = {$value}");
                            $this->structure['begin']['var'][$name] = $value;
                            break;
                        case 'array':
                            if ($value === "<undef>") {
                                $this->structure['begin']['array'][$name] = "<undef>";
                                break;
                            }

                            $parts = explode("\r\n", $value);
                            $fields = [];

                            foreach($parts as $part) {

                            }

                            break;
                        case 'sub':
                            $this->say("\tSet substitution {$name} = {$value}");
                            $this->structure['begin']['sub'][$name] = $value;
                            break;
                        case 'person':
                            $this->say("\tSet person substitution {$name} = {$value}");
                            $this->structure['begin']['person'][$name] = $value;
                            break;
                        default:
                            $this->warn("Unknown definition type {$type}", $filename, $lineno);
                            break;
                    }
                    break;


                case '>':

                    break;


                case '<':

                    break;

                case '+':

                    break;

                case '-':

                    break;

                case '*':

                    break;

                case '%':

                    break;

                case '^':

                    break;

                case '^':

                    break;

                case '@':

                    break;

                default:
                    $this->warn("Unknown command '{$cmd}' (in topic {$topic})", $filename, $lineno);
            }
        }
        print_r($code);

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

    /**
     * Check for patterns in a given string.
     *
     * @param string $regex  The pattern to detect.
     * @param string $string The string that could contain the pattern.
     *
     * @return bool
     */
    private function matchesPattern(string $regex = '', string $string = ''): bool
    {
        preg_match_all($regex, $string, $matches);

        return isset($matches[0][0]);
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
        $this->master->say($message);
    }

    /**
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
}


class Master
{
    public bool $forceCase = true;
    public bool $strict = true;
    public bool $utf8 = false;

    public function say(string $message): void
    {
        echo $message;
    }

    public function warn(string $message, string $filename, int $lineno): void
    {
        $this->say("{$filename}:{$lineno} Error: {$message}\n");
    }
}

class Utils
{
    /**
     * Strip extra whitespace from both ends of the string, and remove
     * line breaks anywhere in the string.
     *
     * @param string $text The text to strip.
     *
     * @return string
     */
    public static function strip(string $text): string
    {
        $text = preg_replace("/^[\s\t]+/", '', $text);
        $text = preg_replace("/[\s\t]+$/", '', $text);
        $text = preg_replace("/[\x0D\x0A]+/", '', $text);
        return $text;
    }
}

$parser = new Parser(new Master());
$parser->parse('admin.rive', file_get_contents('admin.rive'), function ($messge) {
    echo "OnError: " . $messge;
});
