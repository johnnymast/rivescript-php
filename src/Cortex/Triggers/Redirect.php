<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Triggers;

use Axiom\Rivescript\Cortex\Input;

/**
 * Atomic class
 *
 * The Redirect class determines if a provided trigger
 * is a redirect.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Triggers
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class Redirect extends Trigger
{

    /**
     * Parse the trigger.
     *
     * @param string $trigger The trigger to parse.
     * @param Input  $input   Input information.
     *
     * @return bool
     */
    public function parse(string $trigger, Input $input): bool
    {
        $topic = synapse()->memory->shortTerm()->get('topic') ?? 'random';
        $triggers = synapse()->brain->topic($topic)->triggers();

        foreach ($triggers as $info) {
            if ($info['value'] == $trigger) {
                return (is_array($info) && isset($info['redirect']));
            }
        }
        return false;
    }
}
