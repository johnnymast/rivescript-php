<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex;

/**
 * CodeObject class
 *
 * The CodeObject class stores code objects
 *
 * @see      https://www.rivescript.com/wd/RiveScript#object
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class CodeObject
{
    public function __construct(
        protected string $name = '',
        protected string $language = '',
        protected string $code = '',
    )
    {
    }

    /**
     * Return the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Return the code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
