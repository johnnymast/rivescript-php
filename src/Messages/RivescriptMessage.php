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

namespace Axiom\Rivescript\Messages;

class RivescriptMessage
{
    /**
     * @param \Axiom\Rivescript\Messages\MessageType $type    The type of message.
     * @param string                                 $message The message to write.
     * @param array                                  $args    The arguments for the message.
     */
    public function __construct(
        public readonly MessageType $type,
        public readonly string $message,
        public readonly array $args = []
    ) {
    }

    /**
     * Create a Warning message.
     *
     * @param string $message The warning message.
     * @param array  $args    The arguments for this message.
     *
     * @return static
     */
    public static function Warning(string $message, array $args = []): static
    {
        return new static(MessageType::WARNING, $message, $args);
    }

    /**
     * Create a Say message.
     *
     * @param string $message The message to say.
     * @param array  $args    The arguments for this message.
     *
     * @return static
     */
    public static function Say(string $message, array $args = []): static
    {
        return new static(MessageType::SAY, $message, $args);
    }
}