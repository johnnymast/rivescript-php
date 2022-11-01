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

use Axiom\Rivescript\Cortex\Commands\ResponseCommand;
use Axiom\Rivescript\Cortex\Commands\Command;
use Axiom\Rivescript\Cortex\RegExpressions;

/**
 * Person class
 *
 * The Person class is responsible for parsing the {person}...{/person}, <person> tags.
 *
 * @see      https://www.rivescript.com/wd/RiveScript#person-...-person-person
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
class Person extends Tag implements TagInterface
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
    protected string $pattern = RegExpressions::TAG_PERSON;


    /**
     * @param \Axiom\Rivescript\Cortex\Commands\Command $command
     *
     * @return void
     */
    public function parse(Command $command): void
    {
        if ($this->isSourceOfType(self::RESPONSE)) {

            $matches = $this->getMatches($command->getNode());
            $original = synapse()->input->original();
            $content = $command->getNode()->getValue();

            $patterns = synapse()->memory->person()->keys()->all();
            $replacements = synapse()->memory->person()->values()->all();
            $unescaped = [];

            foreach ($patterns as $index => $pattern) {
                $unescaped[$index] = $pattern;
                $patterns[$index] = "/\b" . $pattern . "\b/i";
            }

            foreach ($matches as $match) {
                $didReplaceWith = [];
                $hasCurly = ($match[1] === '{');
                $text = $match[0];

                if ($hasCurly) {
                    $context = trim($match[3]);

                    if (synapse()->memory->person()->has($context)) {
                        $value = synapse()->memory->person()->get($context);

                        if (count($patterns) > 0) {
                            foreach ($patterns as $index => $pattern) {
                                $value = preg_replace($pattern, $replacements[$index], $value);// ?? 'undefined';
                            }
                        }

                        $content = str_replace($text, $value, $content);
                    }
                } else {
                    /**
                     * @var ResponseCommand $command
                     */
                    $trigger = $command->getTrigger();

                    if ($trigger->hasStars()) {
                        if ($trigger->stars->has('<star>')) {
                            $value = $trigger->stars->get('<star>');

                            if (count($patterns) > 0) {
                                foreach ($patterns as $index => $pattern) {
                                    echo "Checking if >{$unescaped[$index]}< was already replaced in {$original}\n";

                                    if (!in_array($unescaped[$index], $didReplaceWith)) {
                                        if ($original == "say You are dumb") echo "NO in {$value}\n";
                                        $value = preg_replace($pattern, $replacements[$index], $value);// ?? 'undefined';

                                        $didReplaceWith[] = $replacements[$index];
                                    } else {
                                        if ($original == "say You are dumb") echo "YES so skipping {$pattern} in {$value}\n";
                                    }
                                }
                            }


                            $content = str_replace($text, $value, $content);
                        }
                    }
                }
            }


            // FIXME: De replace mag alleen op de person tag value.

            $command->setContent($content);
        }
    }
}