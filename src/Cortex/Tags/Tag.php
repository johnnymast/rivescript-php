<?php

namespace Vulcan\Rivescript\Cortex\Tags;

use Vulcan\Rivescript\Contracts\Tag as TagContract;
use Vulcan\Collections\Collection;

abstract class Tag implements TagContract
{
    protected function hasMatches($source)
    {
        preg_match_all($this->pattern, $source, $matches);

        return isset($matches[0][0]);
    }

    protected function getMatches($source)
    {
        if ($this->hasMatches($source)) {
            preg_match_all($this->pattern, $source, $matches, PREG_SET_ORDER);

            return $matches;
        }
    }
}
