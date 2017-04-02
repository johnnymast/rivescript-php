<?php

namespace Vulcan\Rivescript\Cortex\Triggers;

class Wildcard
{
    /**
     * Parse the trigger.
     *
     * @param  string  $trigger
     * @param  string  $message
     * @return array
     */
    public function parse($trigger, $input)
    {
        $wildcards = [
            '/^\*$/'            => '<zerowidthstar>',
            '/\*/'              => '.+?',
            '/#/'               => '\\d+?',
            '/_/'               => '\\w+?',
            '/<zerowidthstar>/' => '.*?'
        ];

        foreach ($wildcards as $pattern => $replacement) {
            $parsedTrigger = preg_replace($pattern, '('.$replacement.')', $trigger);

            if (@preg_match('#^'.$parsedTrigger.'$#u', $message, $stars)) {
                array_shift($stars);

                return [
                    'match' => true,
                    'key'   => $key,
                    'data'  => ['stars' => $stars],
                ];
            }
        }

        return ['match' => false];
    }
}
