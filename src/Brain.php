<?php

namespace Axiom\Rivescript;

/**
 *
 */
class Brain
{
    /**
     *  Whether UTF-8 mode is enabled.
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
     * Teach the Brain with new information.
     *
     * @param resource $stream the stream to read from.
     *
     * @throws \Axiom\Rivescript\Exceptions\ParseException
     * @return void
     */
    public function teach($stream): void
    {
        if (is_resource($stream)) {
            $lineNumber = 0;

            rewind($stream);

            $collect = false;
            $collectFor = null;
            $collectedContent = '';

//            while (!feof($stream)) {
//                $content .= fgets($stream);
//            }

            $str = '';
            while (!feof($stream)) {
                $line = fgets($stream);
                $node = new Node($line, $lineNumber++);

                $command = $node->getCommand();

                if ($command->isEmpty() === true || $command->isComment() === true) {
                    unset($node);
                    continue;
                }

                if ($node->getTag() === '>' && str_starts_with($node->getValue(), "object")) {
                    $collect = true;
                    $collectFor = $command;
                    continue;
                }

                if ($node->getTag() === '<' && str_starts_with($node->getValue(), "object")) {
                    //    $collectedContent .= $node->getOriginalSource();
                    $collectedContent = trim($collectedContent);
                    $collectFor->setContent($collectedContent);

                    $command = $collectFor;
                    $collect = false;
                    $collectFor = null;
                    $collectedContent = '';
                }

                if ($collect === true) {
                    $collectedContent .= $node->getOriginalSource();
                    continue;
                }

                if ($command->isSyntaxValid() === true) {
                    //echo get_class($command) . " -- Topic: " . $this->topic()->getName(), "\n";
                    $command->detect();

                             } else {

                    /**
                     * I am not 100% sure yet on what to do in this case.
                     * For now, we will debug the syntax errors and continue
                     * on our way.
                     */
                    $errors = $command->getSyntaxErrors();

                    throw new ParseException(current($errors));
                }
            }


            /**
             * @deprecated
             */
            //     $this->topics->each(fn(Topic $topic) => $topic->sortTriggers());
//            $this->topics->each(fn(Topic $topic) => $topic->sortTriggers($topic->triggers()));
        }
    }
}