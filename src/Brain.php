<?php

namespace Axiom\Rivescript;

use Axiom\Rivescript\Exceptions\Brain\DeepRecursionException;
use Axiom\Rivescript\Interfaces\Events\EventEmitterInterface;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\Traits\EventEmitter;

/**
 *
 */
class Brain implements EventEmitterInterface
{
    use EventEmitter;

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
        }

        return '';
    }

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