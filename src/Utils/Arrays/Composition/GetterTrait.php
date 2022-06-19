<?php

namespace Pingframework\Boot\Utils\Arrays\Composition;

use Pingframework\Boot\Utils\Arrays\ArrayKeyNotExistsException;
use Pingframework\Boot\Utils\Arrays\ArrayTypeValidationException;

trait GetterTrait
{
    /**
     * @param array  $array
     * @param string $key
     *
     * @return string
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public static function mustGetString(array $array, string $key): string
    {
        self::validate($array, $key);
        return self::mayGetString($array, $key);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @throws ArrayKeyNotExistsException
     */
    private static function validate(array $array, string $key): void
    {
        if (!self::isExists($array, $key)) {
            throw new ArrayKeyNotExistsException('Array key does not exists');
        }
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool
     */
    public static function isExists(array $array, string $key): bool
    {
        return isset($array[$key]);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return string|null
     *
     * @throws ArrayTypeValidationException
     */
    public static function mayGetString(array $array, string $key): ?string
    {
        if (!self::isExists($array, $key)) {
            return null;
        }

        $value = $array[$key];

        if (!is_string($value) && !is_int($value) && !is_float($value) && !is_null($value)) {
            throw new ArrayTypeValidationException('Type error. Value must be a string.');
        }

        return (string)$value;
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return int
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public static function mustGetInt(array $array, string $key): int
    {
        self::validate($array, $key);
        return self::mayGetInt($array, $key);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return int|null
     *
     * @throws ArrayTypeValidationException
     */
    public static function mayGetInt(array $array, string $key): ?int
    {
        if (!self::isExists($array, $key)) {
            return null;
        }

        $value = filter_var($array[$key], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

        if (!is_int($value)) {
            throw new ArrayTypeValidationException('Type error. Value must be an int.');
        }

        return $value;
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return float
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public static function mustGetFloat(array $array, string $key): float
    {
        self::validate($array, $key);
        return self::mayGetFloat($array, $key);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return float|null
     *
     * @throws ArrayTypeValidationException
     */
    public static function mayGetFloat(array $array, string $key): ?float
    {
        if (!self::isExists($array, $key)) {
            return null;
        }

        $value = filter_var($array[$key], FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);

        if (!is_float($value)) {
            throw new ArrayTypeValidationException('Type error. Value must be a float.');
        }

        return $value;
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public static function mustGetBool(array $array, string $key): bool
    {
        self::validate($array, $key);
        return self::mayGetBool($array, $key);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool|null
     *
     * @throws ArrayTypeValidationException
     */
    public static function mayGetBool(array $array, string $key): ?bool
    {
        if (!self::isExists($array, $key)) {
            return null;
        }

        $value = filter_var($array[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (!is_bool($value)) {
            throw new ArrayTypeValidationException('Type error. Value must be a bool.');
        }

        return $value;
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return array
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public static function mustGetArray(array $array, string $key): array
    {
        self::validate($array, $key);
        return self::mayGetArray($array, $key);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return array|null
     *
     * @throws ArrayTypeValidationException
     */
    public static function mayGetArray(array $array, string $key): ?array
    {
        if (!self::isExists($array, $key)) {
            return null;
        }

        $value = $array[$key];

        if (!is_array($value)) {
            throw new ArrayTypeValidationException('Type error. Value must be an array.');
        }

        return $value;
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return mixed
     *
     * @throws ArrayKeyNotExistsException
     */
    public static function mustGetMixed(array $array, string $key): mixed
    {
        self::validate($array, $key);
        return self::mayGetMixed($array, $key);
    }

    /**
     * @param array  $array
     * @param string $key
     *
     * @return mixed
     */
    public static function mayGetMixed(array $array, string $key): mixed
    {
        if (!self::isExists($array, $key)) {
            return null;
        }

        return $array[$key];
    }
}