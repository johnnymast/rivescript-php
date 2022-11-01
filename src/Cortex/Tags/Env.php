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

/**
 * Env class
 *
 * The Env class is responsible for parsing the <env> tag.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#env
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
class Env extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_ENV;

    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {

            $matches = $this->getMatches($command->getNode());
            $content = $command->getNode()->getValue();

            //if (is_array($matches)) {
                foreach ($matches as $match) {
                    $string = $match[0];
                    $isSetter = !empty($match[1]);

                    if ($isSetter === true) {
                        $value = trim($match[2]);
                        $key = trim($match[1]);

                        synapse()->memory->global()->put($key, $value);

                        $content = str_replace($string, '', $content);

                    } else {
                        $value = "undefined";
                        $key = trim($match[3]);

                        if (synapse()->memory->global()->has($key)) {
                            $value = synapse()->memory->global()->get($key);
                        }

                        $content = str_replace($string, $value, $content);
                    }
                }

                $command->setContent($content);
            //}

        }
    }
}