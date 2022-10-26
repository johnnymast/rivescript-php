<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Johnny Mast <mastjohnny@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\RegExpressions;
use Axiom\Rivescript\Utils\Misc;

/**
 * Reply class
 *
 * The Reply class is responsible for parsing the <reply> tag.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#input1---input9-reply1---reply9
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Reply extends Tag implements TagInterface
{

    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = [self::RESPONSE];

    /**
     * The pattern for this tag.
     *
     * @var string
     */
    protected string $pattern = RegExpressions::TAG_REPLY;

    /**
     * All input signs in a row.
     *
     * @var array
     */
    protected array $stars = [
        ['reply', 'reply1'],
        'reply2',
        'reply3',
        'reply4',
        'reply5',
        'reply6',
        'reply7',
        'reply8',
        'reply9'
    ];

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {
            $value = $command->getNode()->getValue();
            $matches = $this->getMatches($command->getNode());
            $replies = array_values(synapse()->memory->replies()->all());

            /**
             * First of replace all the replies with a colon before
             * it. This is so we can use it in our format string function
             * later.
             */
            $results = [];
            foreach ($matches as $match) {
                $reply = $match[0];
                $replace = str_replace(['<', '>'], ['[', ']'], $reply);
                $number = (int)substr($reply, -2, 1);

                if ($number < 2) {
                    $results[$replace] = $replies[0] ?? "undefined";;
                    $value = str_replace(["<reply>", "<reply1>"], [":[reply]", ":[reply1]"], $value);
                } else {
                    $results[$replace] = $replies[$number-1] ?? "undefined";
                    $value = str_replace("<reply{$number}>", ":[reply{$number}]", $value);
                }
            };

            $content = Misc::formatString($value, $results);
            $command->setContent($content);
        }
    }
}