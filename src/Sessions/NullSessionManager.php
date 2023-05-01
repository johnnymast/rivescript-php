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

namespace Axiom\Rivescript\Sessions;

use Axiom\Rivescript\Interfaces\Sessions\SessionInterface;
use Axiom\Rivescript\Interfaces\Sessions\SessionManagerInterface;

/**
 * NullSessionManager class
 *
 * The null session manager doesn't store any user variables.
 *
 * This is used by the unit tests and isn't practical for real world usage,
 * as the bot would be completely unable to remember any user variables or
 * history.
 *
 * PHP version 8.1 and higher.
 *
 * @category Core
 * @package  Sessions
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class NullSessionManager implements SessionManagerInterface
{
    public function freeze(string $username): void
    {
        // Empty for testing purposes.
    }

    public function get(string $username, string $key, string $default = "undefined"): string|null
    {
        return $default;
    }

    public function getAll(): array
    {
        return [];
    }

    public function getAny(string $username): array
    {
        return [];
    }

    public function reset(string $username): void
    {
        // Empty for testing purposes.
    }

    public function resetAll(): void
    {
        // Empty for testing purposes.
    }

    public function set(string|null $username = null, array $args = []): void
    {
        // Empty for testing purposes.
    }

    public function thaw(string $username, string $action = "thaw"): void
    {
        // Empty for testing purposes.
    }

    public function defaultSession(): array
    {
        return [];
    }
}