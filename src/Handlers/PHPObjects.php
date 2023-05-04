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

namespace Axiom\Rivescript\Handlers;

use Axiom\Rivescript\Interfaces\Handlers\HandlerInterface;
use Axiom\Rivescript\Messages\RivescriptMessage;
use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\RivescriptErrors;
use Axiom\Rivescript\RivescriptEvent;

/**
 * PHPObjects class
 *
 * This class handles the execution of php code.
 *
 * PHP version 8.1 and higher.
 *
 * @category Core
 * @package  Handers
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class PHPObjects implements HandlerInterface
{

    protected array $objects = [];

    protected string $name = '';
    protected mixed $code = '';


    /**
     * @param \Axiom\Rivescript\Rivescript $master
     */
    public function __construct(protected readonly Rivescript $master)
    {
    }

    /**
     * Load the code object.
     *
     * @param string          $name The name of the object.
     * @param string|callable $code The code for the object or a closure object.
     *
     * @return void
     */
    public function load(string $name, mixed $code): void
    {
        if (is_callable($code)) {
            $this->objects[$name] = $code;
        } else {
            try {
                $source = '$this->objects["' . $name . '"] = function ($rs, $args) use ($code) {' . "\n" . $code . "\n" . '};' . "\n";
                eval($source);
            } catch (\Exception $e) {
                $this->master->emit(
                    RivescriptEvent::OUTPUT,
                    RivescriptMessage::Warning("Error evaluating Php object: :error", ['error' => $e->getMessage()])
                );
            }
        }
    }

    /**
     * Execute the code.
     *
     * @param \Axiom\Rivescript\Rivescript $rs     The Rivescript interpreter.
     * @param string                       $name   the name of the object being called.
     * @param array                        $fields array of arguments passed to the object.
     *
     * @return string|null
     */
    public function call(Rivescript $rs, string $name, array $fields = []): ?string
    {
        if (!$this->objects[$name]) {
            return RivescriptErrors::OBJECT_NOT_FOUND->value;
        }

        $func = $this->objects[$name];

        try {
            $reply = call_user_func_array($func, $fields);
        } catch (\Exception $e) {
            $reply = "[ERR: Error when executing JavaScript object: {$e->getMessage()}]";
        }

        if (is_null($reply)) {
            $reply = "";
        }

        return $reply;
    }
}