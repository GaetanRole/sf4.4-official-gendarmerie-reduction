<?php

declare(strict_types=1);

namespace App\Utils;

use \ReflectionClass;
use \ReflectionException;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
abstract class AbstractBasicEnum
{
    /** @var array */
    private static $constCacheArray;

    /**
     * @throws ReflectionException
     */
    private static function getConstants(): array
    {
        null !== self::$constCacheArray ?: [];

        $calledClass = static::class;

        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * @throws ReflectionException
     */
    public static function isValidName(string $name): bool
    {
        return in_array(
            strtolower($name),
            array_map('strtolower', array_keys(self::getConstants())),
            true
        );
    }

    /**
     * @throws ReflectionException
     */
    public static function isValidValue(string $value): bool
    {
        return in_array($value, array_values(self::getConstants()), true);
    }
}
