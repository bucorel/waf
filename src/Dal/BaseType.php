<?php
namespace Bucorel\Waf\Dal;

abstract class BaseType{
    /**
     * Return all constants defined in the type.
     */
    public static function all(): array{
        $calledClass = get_called_class();
        $reflection = new \ReflectionClass($calledClass);
        return $reflection->getConstants();
    }

    /**
     * Return [value => label] map for dropdowns or display.
     */
    public static function options(): array{
        return static::all();
    }

    /**
     * Validate a given value.
     */
    public static function isValid($value): bool{
        return in_array($value, static::all(), true);
    }

    /**
     * Get the constant key name for a given value.
     */
    public static function getKey($value): ?string{
        return array_search($value, static::all(), true) ?: null;
    }
}
?>
