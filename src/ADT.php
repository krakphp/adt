<?php

namespace Krak\ADT;

abstract class ADT
{
    /** return the type names of the different instances */
    abstract public static function types(): array;

    public function match(array $matches) {
        $type = get_class($this);
        $this->assertMatchesContainAllCases($matches);
        $this->assertTypeIsRegistered($type);

        $match = $matches[$type];
        if (is_callable($match)) {
            return $match($this);
        }

        return $match;
    }

    public function matchWithDefault(array $matches, $default = null) {
        $type = get_class($this);
        $this->assertTypeIsRegistered($type);

        $match = $matches[$type] ?? $default;
        if (is_callable($match)) {
            return $match($this);
        }

        return $match;
    }

    private function assertTypeIsRegistered(string $type) {
        if (!in_array($type, static::types())) {
            throw new \RuntimeException("Type {$type} is not registered in the list of valid types for this ADT.");
        }
    }

    private function assertMatchesContainAllCases(array $matches) {
        foreach (static::types() as $type) {
            if (!array_key_exists($type, $matches)) {
                throw new \RuntimeException("Case {$type} is not handled in this match statement.");
            }
        }
    }
}
