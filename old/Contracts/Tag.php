<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Contracts;

use Axiom\Rivescript\Cortex\Input;

/**
 * Tag interface
 *
 * The Tag interface is used by Tag parsers.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Contracts
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
interface Tag
{
    /**
     * Parse the response.
     *
     * @param string $source The string containing the Tag.
     * @param Input  $input  The input information.
     *
     * @return string
     */
    public function parse(string $source, Input $input): string;


    /**
     * Return the tag that the class represents.
     *
     * @return mixed
     */
    public function getTagName();
}
