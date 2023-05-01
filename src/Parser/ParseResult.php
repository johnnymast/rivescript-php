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

use Axiom\Rivescript\Messages\MessageType;
use Axiom\Rivescript\Messages\RivescriptMessage;

class ParseResult
{

    /**
     * @param \Axiom\Rivescript\Parser\ParseResultStatus        $status  The parse status.
     * @param mixed                                             $result  The parse result.
     * @param \Axiom\Rivescript\Messages\RivescriptMessage|null $message A message to send out.
     */
    public function __construct(
        public readonly ParseResultStatus $status,
        public mixed $result = null,
        public readonly ?RivescriptMessage $message = null
    ) {
    }

    /**
     * Wrapper / alias for withSuccess.
     *
     * @param mixed $result The parse result.
     *
     * @return static
     */
    public static function with(mixed $result): self
    {
        return self::withSuccess($result);
    }

    /**
     * @param \Axiom\Rivescript\Parser\ParseResultStatus $status The parse status.
     * @param mixed                                      $result The parse result.
     *
     * @return static
     */
    public static function withStatus(ParseResultStatus $status, mixed $result): self
    {
        return new static(
            status: $status,
            result: $result
        );
    }

    /**
     * Return a status with a success status.
     *
     * @param mixed $result The parse result.
     *
     * @return static
     */
    public static function withSuccess(mixed $result): self
    {
        return new static(
            status: ParseResultStatus::SUCCESS,
            result: $result
        );
    }

    /**
     * Return a status with an warning message.
     *
     * @param string $message The message to report.
     * @param array  $args    The arguments for the message.
     * @param mixed  $result  The parse result.
     *
     * @return static
     */
    public static function withWarning(string $message, array $args = [], mixed $result = null): self
    {
        return new static(
            status: ParseResultStatus::WARNING,
            result: $result,
            message: new RivescriptMessage(
                type: MessageType::WARNING,
                message: $message,
                args: $args
            )
        );
    }

    /**
     * Return a status with an error message.
     *
     * @param string $message The message to report.
     * @param array  $args    The arguments for the message.
     *
     * @return static
     */
    public static function withError(string $message, array $args): self
    {
        return new static(
            status: ParseResultStatus::ERROR,
            message: new RivescriptMessage(
                type: MessageType::ERROR,
                message: $message,
                args: $args
            )
        );
    }
}