<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\ResponseQueue;

/**
 * ResponseQueueItem class
 *
 * The ResponseQueueItem represents one response in the ResponseQueue.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\ResponseQueue
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class ResponseQueueItem
{

    /**
     * The command prefix.
     *
     * @var string
     */
    public string $command = '';

    /**
     * The response type.
     *
     * @var string
     */
    public string $type = 'atomic';

    /**
     * The sort order of the response.
     *
     * @var int
     */
    public int $order = 0;

    /**
     * Local parser options at this item.
     *
     * @var array<string, string>
     */
    public array $options = [];

    /**
     * ResponseQueueItem constructor.
     *
     * @param string               $command The command prefix.
     * @param string               $type    The type of response.
     * @param int                  $order   The order of the response.
     * @param array<string,string> $options The local interpreter options.
     */
    public function __construct(string $command, string $type, int $order = 0, array $options = [])
    {
        $this->command = $command;
        $this->type = $type;
        $this->order = $order;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }
}
