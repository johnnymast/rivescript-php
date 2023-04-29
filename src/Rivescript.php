<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript;

use Axiom\Rivescript\ContentLoader\ContentLoader;

/**
 * Rivescript class
 *
 * @method void load(string|array $path)
 * @method void loadDirectory(string $path)
 * @method void loadFile(string $filename)
 * @method mixed getStream()
 *
 *
 * The entry point for using the interpreter.
 *
 * PHP version 8.1 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Rivescript extends ContentLoader
{

    /**
     * A RiveScript interpreter for PHP.
     *
     * @param bool $debug  Set to true to enable verbose logging to standard out.
     * @param bool $strict Enable strict mode. Strict mode causes RiveScript syntax
     *                     errors to raise an exception at parse time. Strict mode is on
     *                     true by default.
     * @param int  $depth  Enable strict mode. Strict mode causes RiveScript syntax
     *                     errors to raise an exception at parse time. Strict mode is on
     *                     true by default.
     * @param bool $utf8   Enable UTF-8 mode. When this mode is enabled, triggers in
     *                     RiveScript code are permitted to contain foreign and special
     *                     symbols. Additionally, user messages are allowed to contain most
     *                     symbols instead of having all symbols stripped away. This is
     *                     considered an experimental feature because all the edge cases of
     *                     supporting Unicode haven't been fully tested. This option
     *                     is false by default.
     */
    public function __construct(
        protected bool $debug = false,
        protected bool $strict = true,
        protected int $depth = 50,
        protected bool $utf8 = false
    )  //. session manager,
    {
    }
}
