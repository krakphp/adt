<?php

namespace Krak\ADT;

abstract class ADT
{
    private static $staticConstructorMethodCache = [];

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

    public static function __callStatic(string $name, array $arguments) {
        self::initStatitConstructorMethodCache();
        $class = get_called_class();
        if (!isset(self::$staticConstructorMethodCache[$class][$name])) {
            throw new \BadMethodCallException("Method constructor {$name} does not exist for this ADT. Valid static constructors are: " . implode(', ', array_keys(self::$staticConstructorMethodCache[$class])));
        }

        $className = self::$staticConstructorMethodCache[$class][$name];
        return new $className(...$arguments);
    }

    private static function initStatitConstructorMethodCache(): void {
        $class = get_called_class();

        if (isset(self::$staticConstructorMethodCache[$class])) {
            return;
        }

        foreach (static::types() as $type) {
            $finalNsSep = strrpos($type, '\\');
            if ($finalNsSep === false) {
                self::$staticConstructorMethodCache[$class][lcfirst($type)] = $type;
            } else {
                self::$staticConstructorMethodCache[$class][lcfirst(substr($type, $finalNsSep + 1))] = $type;
            }
        }
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
