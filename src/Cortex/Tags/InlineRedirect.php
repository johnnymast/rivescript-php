<?php

/**
 * Class InlineRedirect handling the {@}/<@> tag.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Tags
 * @author       Johnny Mast <mastjohnny@gmail.com>
 */

namespace Axiom\Rivescript\Cortex\Tags;

use Axiom\Rivescript\Cortex\Input;

/**
 * Class InlineRedirect
 */
class InlineRedirect extends Tag
{
    /**
     * @var array<string>
     */
    protected $allowedSources = ['response'];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected $pattern = '/({)@(.+?)(})|(<)@(>)/u';

    /**
     * Parse the source.
     *
     * @param  string  $source  The string containing the Tag.
     * @param  Input   $input   The input information.
     *
     * @return string
     */
    public function parse(string $source, Input $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source)[0];
            $wildcards = synapse()->memory->shortTerm()->get('wildcards');

            $trigger = null;
            $target = null;
            $key = null;

            if ($matches[0] === "<@>" and is_array($wildcards) === true and count($wildcards) > 0) {
                $target = $wildcards[0];

                $key = synapse()->memory->shortTerm()->get('trigger');
                $trigger = synapse()->brain->topic()->triggers()->get($key);
            } elseif ($matches[1] === '{') {
                $target = $matches[2];

                $key = $input->source();
                $trigger = synapse()->brain->topic()->triggers()->get($key);
            }

            if (is_null($trigger) === false && is_null($key) === false && is_null($target) === false) {
                $trigger['redirect'] = $target;

                synapse()->brain->topic()->triggers()->put($key, $trigger);
                $source = str_replace($matches[0], '', $source);
            }
        }

        return $source;
    }
}
