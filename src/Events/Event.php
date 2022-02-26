<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Events;

/**
 * Event class
 *
 * This class defines some default Rivescript-php
 * events.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Events
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Event
{
    /**
     * General debug statement.
     */
    public const DEBUG = 'debug';

    /**
     * A verbose statement.
     */
    public const DEBUG_VERBOSE = 'debug_verbose';

    /**
     * A warning statement.
     */
    public const DEBUG_WARNING = 'debug_warning';

}