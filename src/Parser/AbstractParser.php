<?php

namespace Axiom\Rivescript\Parser;

class AbstractParser
{

    /**
     * Create a parser context.
     *
     * @return object
     */
    private function createParserContext(): object
    {
        return (object)[
            'topic' => "random",
            'trigger' => null,
        ];
    }

    /**
     * Create an empty object label.
     *
     * @param string $type The type of object to create.
     *
     * @return array|null
     */
    private function createEmptyLabel(string $type): ?array
    {
        return match ($type) {
            "code" => ["type" => "code", "valid" => false, "name" => "", "language" => "", "lines" => []],
            "topic" => ["type" => "topic", "valid" => false, "lines" => []],
            "begin" => ["type" => "begin", "valid" => false, "lines" => []],
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
    private function hasTopic(string $name): bool
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
    private function createTopic(string $name): array
    {
        $this->values['topics'][$name] = [
            "includes" => [],
            "inherits" => [],
            "triggers" => [],
        ];
        return $this->values['topics'][$name];
    }

}