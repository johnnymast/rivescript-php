<?php

namespace Axiom\Rivescript;

use Axiom\Rivescript\Exceptions\Brain\DeepRecursionException;
use Axiom\Rivescript\Interfaces\Events\EventEmitterInterface;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\Traits\EventEmitter;
use Axiom\Rivescript\Traits\Regex;
use Axiom\Rivescript\Utils\Str;

/**
 *
 */
class Brain implements EventEmitterInterface
{
    use EventEmitter;
    use Regex;

    /**
     * @var string|null
     */
    private ?string $currentUser = null;

    /**
     * The Brain class controls the actual reply fetching phase for RiveScript.
     *
     * @param \Axiom\Rivescript\Rivescript $master A reference to the parent RiveScript instance.
     * @param bool                         $strict Whether strict mode is enabled.
     * @param bool                         $utf8   Whether UTF-8 mode is enabled.
     */
    public function __construct(
        protected readonly Rivescript $master,
        protected readonly bool $strict = false,
        protected readonly bool $utf8 = false
    ) {
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
     * Fetch a reply from the RiveScript brain.
     *
     * @param string $user              A unique user ID for the person requesting a reply.
     *                                  This could be e.g. a screen name or nickname. It's used internally
     *                                  to store user variables (including topic and history), so if your
     *                                  bot has multiple users each one should have a unique ID.
     * @param string $msg               A unique user ID for the person requesting a reply.
     *                                  This could be e.g. a screen name or nickname. It's used internally
     *                                  to store user variables (including topic and history), so if your
     *                                  bot has multiple users each one should have a unique ID.
     * @param bool   $errors_as_replies When errors are encountered (such as a
     *                                  deep recursion error, no reply matched, etc.) this will make the
     *                                  reply be a text representation of the error message. If you set
     *                                  this to false, errors will instead raise an exception, such as
     *                                  a ``DeepRecursionException`` or ``NoReplyErrorException``. By default, no
     *                                  exceptions are raised and errors are set in the reply instead.
     *
     * @return string
     */
    public function reply(string $user, string $msg, bool $errors_as_replies): string
    {
        $this->output(
            RivescriptMessage::Say(
                "Get reply to [:user] :msg",
                ['user' => $user, 'msg' => $msg]
            )
        );

        if (isset($this->master->topics['__begin__'])) {
            try {
                $begin = $this->getReply($user, 'request', 'begin', ignore_object_errors: $errors_as_replies);
            } catch (\Exception $e) { // RiveScriptError

            }
        }

        return 'abc';
    }


    /**
     * The internal reply getter function.
     *
     * @param string $user                 The user ID as passed to reply().
     * @param string $msg                  The formatted user message.
     * @param string $context              The reply context, one of begin or normal.
     * @param int    $step                 The recursion depth counter.
     * @param bool   $ignore_object_errors Whether to ignore errors from within
     *                                     Php object macros and not raise an ObjectError exception.
     *
     * @return string The reply output.
     */
    private function getReply(
        string $user,
        string $msg,
        string $context = 'normal',
        int $step = 0,
        bool $ignore_object_errors = true
    ): string {
        if (!isset($this->master->sorted['topics'])) {
            throw new NoReplyErrorException(
                "You must call sortReplies() once you are done loading RiveScript documents"
            );
        }

        $topic = $this->master->getUserVar($user, "topic");

        if ($topic == null || $topic == "undefined") {
            $topic = "random";
            $this->master->setUserVar($user, "topic", $topic);
        }

        /**
         * Collect data on the user.
         */
        $stars = [];
        $thatstars = [];  # For %Previous's.
        $reply = '';

        if (!isset($this->master->topics[$topic])) {
            $this->output(
                RivescriptMessage::Say(
                    "User :user was in an empty topic named':topic'",
                    ['user' => $user, 'topic' => $topic]
                )
            );

            $topic = "random";
            $this->master->setUserVar($user, "topic", $topic);
        }

        if ($step > $this->master->depth) {
            throw new DeepRecursionException();
        }

        if ($$context == 'begin') {
            $topic = '__begin__';
        }

        $history = $this->master->getUserVar($user, "__history__");
        if (!is_array($history) || !isset($history['input']) || !isset($history['reply'])) {
            $history = $this->defaultHistory();
            $this->master->setUserVar($user, "__history__", $history);
        }

        if (!isset($this->master->topics['random'])) {
            throw new NoReplyErrorException("no default topic 'random' was found");
        }

        $matched = null;
        $matchedTrigger = null;
        $foundMatch = false;

        if ($step == 0) {
            $allTopics = [$topic];

            if (isset($this->master->includes[$topic]) || isset($this->master->lineage[$topic])) {
                $allTopics = getTopicTree($this->master, $topic);
            }

            foreach ($allTopics as $top) {
                $this->output(RivescriptMessage::Say("Checking topic :topic for any %Previous's.", ['topic' => $top]));
            }

            $lastReply = $history["reply"][0];

            foreach ($this->master->sorted['thats'][$topic] as $trig) {
                $pattern = $trig[1]["previous"];
                $botside = $this->replyRegexp($user, $pattern);
            }
        }

        return '';
    }

    /**
     * Prepares a trigger for the regular expression engine.
     *
     * @param string $user    The user ID invoking a reply.
     * @param string $pattern The original trigger text to be turned into a regexp.
     *
     * @return string The final regexp object.
     */
    private function replyRegexp(string $user, string $pattern): string
    {
        /**
         * Convert # into (\d+?)
         * Convert _ into (\w+?)
         * Remove {weight} tags
         * Remove empty entities
         * Remove empty entities from start of alt/pattern:opts
         * Remove empty entities from end of alt/oppattern:ts
         */
        $regexp = $this->replacePatternWith(pattern: '/\*/', source: $regexp, replacement: "(.+?)");
        $regexp = $this->replacePatternWith(pattern: '/#/', source: $regexp, replacement: "(\\d+?)");
        $regexp = $this->replacePatternWith(pattern: '/_/', source: $regexp, replacement: "(\\w+?)");
        $regexp = $this->replacePatternWith(pattern: '/\s*\{weight=\d+\}\s*/', source: $regexp, replacement: "");
        $regexp = $this->replacePatternWith(pattern: '/<zerowidthstar>/', source: $regexp, replacement: "(.*?)");
        $regexp = $this->replacePatternWith(pattern: '/\|{2,}/', source: $regexp, replacement: '|');
        $regexp = $this->replacePatternWith(pattern: '/(\(|\[)\|/', source: $regexp, replacement: '$1');
        $regexp = $this->replacePatternWith(pattern: '/\|(\)|\])/', source: $regexp, replacement: '$1');

        // @ symbols conflict w/ arrays
        if ($this->utf8) {
            $regexp = $this->replacePatternWith(pattern: '/\\@/', source: $regexp, replacement: "\\u0040");
        }

        $pattern = '/\[(.+?)\]/';
        $giveup = 0;

        if ($this->matchesPattern(pattern: $pattern, source: $regexp)) {
            $matches = $this->getMatchesFromPattern(pattern: $pattern, source: $regexp);
            foreach ($matches as $match) {
                //   $regexp = $this->replacePatternWith(pattern: $pattern, source: $regexp, replacement: $match[1]);
                $parts = explode('|', $match[1]);
                $new = [];

                foreach ($parts as $part) {
                    $p = '(?:\\s|\\b)+' . trim($part) . '(?:\\s|\\b)+';
                    $new[] = $p;
                }

                /**
                 * If this optional had a star or anything in it, make it
                 * non-matching.
                 */
                $pipes = implode('|', $new);
                $pipes = $this->replacePatternWith(pattern: '(.+?)', source: $pipes, replacement: '(?:.+?)');
                $pipes = $this->replacePatternWith(pattern: '(\d+?)', source: $pipes, replacement: '(?:\d+?)');
                $pipes = $this->replacePatternWith(pattern: '([A-Za-z]+?)', source: $pipes, replacement: '(?:[A-Za-z]+?)');


                $regexp = $this->replacePatternWith(
                    '/\s*\[' . preg_quote($match) . '\]\s*/',
                    source: $regexp,
                    replacement: '(?:' . $pipes . '|(?:\\s|\\b))'
                );
                $regexp = $this->replacePatternWith(pattern: '/\w/', source: $regexp, replacement: '[^\s\d]');

                $bvars = $this->getMatchesFromPattern(
                    pattern: '/' . preg_quote('<bot (.+?)>', '/') . '/',
                    source: $regexp
                );


                foreach ($bvars as $var) {
                    $rep = '';
                    if (isset($this->master->var[$var])) {
                        $rep = $this->formatMessage($this->master->var[$var]);
                    }

                    $regexp = $this->replacePatternWith(pattern: "<bot {$var}>", source: $regexp, replacement: $rep);
                }

                /**
                 *  Filter in user variables.
                 */
                $uvars = $this->getMatchesFromPattern(
                    pattern: '/' . preg_quote('<get (.+?)>', '/') . '/',
                    source: $regexp
                );

                foreach ($uvars as $var) {
                    $rep = '';
                    $value = $this->master->getUserVar($user, $var);
                    if ($value !== null && $value  !== "undefined") {
                        $rep = Str::stripNasties($value);
                    }

                    $regexp = $this->replacePatternWith(pattern: "<get {$var}>", source: $regexp, replacement: $rep);
                }

                /**
                 * Filter in <input> and <reply> tags. This is a slow process, so only
                 * do it if we have to!
                 */
                if (strpos($regexp, '<input') > -1 || strpos($regexp, '<reply') > -1) {
                    $history = $this->master->getUserVar($user, "__history__");

                }
            }
        }


        return '';
    }

    /**
     * Format a user's message for safe processing.
     *
     * This runs substitutions on the message and strips out any remaining
     * symbols (depending on UTF-8 mode).
     *
     * @param string $msg      The user's message.
     * @param bool   $botreply Whether this formatting is being done for the
     *                         bot's last reply (e.g. in a ``%Previous`` command).
     *
     * @return string The formatted message.
     */
    private function formatMessage(string $msg, bool $botreply = false): string
    {
        $msg = strtolower($msg);
        $msg = $this->substitute($msg);

        if ($this->utf8) {
            $msg = $this->replacePatternWith(pattern: '/[\\<>]+/', source: $msg, replacement: '');


//            if (self.master.unicodePunctuation != null) {
//                msg = msg.replace(self.master.unicodePunctuation, "");
//            }
            $this->output(
                RivescriptMessage::Say(
                    "Implement unicodePunctuation in Rivescript.php and use in in Brain::formatMessage "
                )
            );

            if ($botreply) {
                $msg = $this->replacePatternWith(pattern: '/[.?,!;:@#$%^&*()]/', source: $msg, replacement: '');
            }
        } else {
            $msg = Str::stripNasties($msg, $this->utf8);
        }

        $msg = trim($msg);
        $msg = $this->replacePatternWith('/\s+/', $msg, " ");
        return $msg;
    }


    /**
     * Run a kind of substitution on a message.
     *
     * @param string $msg  The message to run substitutions against.
     * @param string $type The type of substitution to run,
     *                     one of ``subs`` or ``person``.
     *
     * @return string
     */
    private function substitute(string $msg, string $type): string
    {
        if (!isset($this->master->sorted[$type])) {
            $this->output(RivescriptMessage::Warning("You forgot to call sortReplies()!"));
            return "";
        }

        $subs = ($type == 'sub') ? $this->master->sub : $this->master->person;

//
//        if (self.master.unicodePunctuation != null) {
//            pattern = msg.replace(self.master.unicodePunctuation, "");
//        } else {
//            pattern = msg.replace(/[.,!?;:]/g, "");
//		}


        $this->output(
            RivescriptMessage::Say("Implement unicodePunctuation in RiveScript.php! and use it in Brain::substitute()")
        );
        $this->output(
            RivescriptMessage::Say("Implement maxwords in RiveScript.php! and use it in Brain::substitute()")
        );

        $pattern = $this->replacePatternWith(pattern: '/[.,!?;:]/', source: $msg, replacement: '');

        $tries = 0;
        $giveup = 0;
        $subgiveup = 0;
        $maxwords = 0; // FIXME: Make dynamic

        while (strpos($pattern, ' ') > -1) {
            $giveup++;
            // Give up if there are too many substitutions (for safety)
            if ($giveup >= 1000) {
                $this->output(RivescriptMessage::Warning("Too many loops when handling substitutions!"));
                break;
            }

            $li = Misc::nIndexOf($pattern, " ", $maxwords);
            $subpattern = strstr($pattern, 0, $li);

            $result = $subs[$subpattern];
            if ($result) {
                $msg = $this->replacePatternWith(pattern: $subpattern, source: $msg, replacement: $result);
            } else {
                while (strpos($subpattern, " ") > -1) {
                    $subgiveup++;

                    if ($subgiveup >= 1000) {
                        $this->output(RivescriptMessage::Warning("Too many loops when handling substitutions!"));
                        break;
                    }

                    $li = strrpos($subpattern, " ");
                    $subpattern = substr($subpattern, 0, $li);

                    $result = $subs[$subpattern];
                    if ($result) {
                        $msg = $this->replacePatternWith(pattern: $subpattern, source: $msg, replacement: $result);
                        break;
                    }

                    $tries++;
                }
            }

            $fi = strpos($pattern, " ");
            $pattern = substr($pattern, 0, $fi + 1);
            $tries++;
        }

        // After all loops, see if just one word is in the pattern
        $result = $subs[$pattern];
        if ($result) {
            $msg = $this->replacePatternWith(pattern: $pattern, source: $msg, replacement: $result);
        }

        return $msg;
    }


    /**
     * @return array[]
     */
    private function defaultHistory()
    {
        return [
            'input' => [
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined"
            ],
            'reply' => [
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined",
                "undefined"
            ]
        ];
    }

}