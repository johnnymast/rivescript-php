<?php

/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Axiom\Rivescript;


use Axiom\Rivescript\Messages\RivescriptMessage;


/**
 * Given one topic, get the list of all included/inherited topics.
 *
 * @param \Axiom\Rivescript\Rivescript $rivescript Reference to the Rivescript instance.
 * @param string                       $topic      The topic to start the search at.
 * @param int                          $depth      The recursion depth counter.
 *
 * @throws \Exception
 *
 * @return array  Array of topics.
 */
function getTopicTree(Rivescript $rs, string $topic, int $depth = 0): array
{
    if ($depth > $rs->depth) {
        $rs->warn("Deep recursion while scanning topic trees!");
        return [];
    }

    /**
     * Collect an array of all topics.
     */
    $topics = [$topic];
    $includes = sort($rs->includes);

    if (isset($includes[$topic])) {
        $sorted = $rs->includes[$topic];
        sort($sorted);

        foreach ($sorted as $includes) {
            $topics = array_merge(
                $topics,
                getTopicTree($rs, $includes, $depth + 1)
            ); // FIXME: Rename $includes to a singular name?
        }

        if (isset($rs->lineage[$topic])) {
            $sorted = $rs->lineage[$topic];
            sort($sorted);

            foreach ($sorted as $inherits) {
                $topics = array_merge(
                    $topics,
                    getTopicTree($rs, $inherits, $depth + 1)
                ); // FIXME: Rename $inherits to a singular name?
            }
        }
    }

    return $topics;
}