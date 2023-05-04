<?php

namespace Axiom\Rivescript\Parser;

class AbstractParser
{

    /**
     * Create a parser context.
     *
     * @return object
     */
    protected function createParserContext(): object
    {
        return (object)[
            'topic' => "random",
            'lastTopic' => "random",
            'trigger' => null,
            'label' => null,
        ];
    }

    /**
     * Create an empty object label.
     *
     * @param string $type The type of object to create.
     *
     * @return array|null
     */
    protected function createEmptyLabel(string $type): ?array
    {
        return match ($type) {
            "code" => ["type" => "code", "name" => "", "language" => "", "lines" => []],
            "topic" => ["type" => "topic", "lines" => []],
            "begin" => ["type" => "begin", "lines" => []],
            default => null,
        };
    }

    /**
     * Check if a topic exists.
     *
     * @param string $name The name of the topic.
     *
     * @return bool
     */
    protected function hasTopic(string $name): bool
    {
        return isset($this->values["topics"][$name]);
    }

    /**
     * Create a new topic.
     *
     * @param string $name The name of the topic.
     *
     * @return array
     */
    protected function initTopic(string $name): array
    {
        if (!$this->hasTopic($name)) {
            $this->values['topics'][$name] = [
                "includes" => [],
                "inherits" => [],
                "triggers" => [],
            ];
        }
        return $this->values['topics'][$name];
    }

}