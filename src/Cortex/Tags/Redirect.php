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

use Axiom\Rivescript\Cortex\Input as SourceInput;

/**
 * InlineRedirect class
 *
 * This class is responsible parsing the <@> tag.
 *
 * PHP version 7.4 and higher.
 *
 * @category Core
 * @package  Cortext\Tags
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.4.0
 */
class Redirect extends Tag
{
    /**
     * Determines where this tag is allowed to
     * be used.
     *
     * @var array<string>
     */
    protected array $allowedSources = ["response"];

    /**
     * Regex expression pattern.
     *
     * @var string
     */
    protected string $pattern = "/({)@(.+?)(})|(<)@(>)/u";

    /**
     * Parse the source.
     *
     * @param string      $source The string containing the Tag.
     * @param SourceInput $input  The input information.
     *
     * @return string
     */
    public function parse(string $source, SourceInput $input): string
    {
        if (!$this->sourceAllowed()) {
            return $source;
        }

        if ($this->hasMatches($source)) {
            $matches = $this->getMatches($source);
            $wildcards = synapse()->memory->shortTerm()->get("wildcards");


            // Open issue: https://play.rivescript.com/s/WEszGV1yMg
            // Question open.

            foreach ($matches as $redirect) {
                $tag = $redirect[0];

                if ($tag == "<@>" && is_array($wildcards) === true && count($wildcards) > 0) {

                    /**
                     * Handle <@>, this is an alias for {@<star>}
                     */
                    if (isset($wildcards[0]) === true) {
                        $response = synapse()->memory->shortTerm()->get("response");

                        if (is_object($response) === true) {
                            $response->setRedirect($wildcards[0]);
                            $source = str_replace($tag, '', $source);
                        }
                    }
                    /**
                     * Break for now.
                     * Maybe newer rivescript versions
                     * allow more <@> tags (but i dont see why).
                     */
                    break;
                } elseif ($tag[0] == '{') {
                    /*
                     * Handle {@target} there
                     */
                    $location = trim($redirect[2]);
                    $response = synapse()->memory->shortTerm()->get("response");

                    if (is_object($response) === true) {
                        $response->setRedirect($location);
                        $source = str_replace($tag, '', $source);
                    }
                }
            }
        }

        return $source;
    }

    /**
     * Return the tag that the class represents.
     *
     * @return string
     */
    public function getTagName(): string
    {
        return "redirect";
    }
}
