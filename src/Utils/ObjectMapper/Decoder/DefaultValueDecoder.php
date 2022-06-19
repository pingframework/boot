<?php

/**
 * Ping Boot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Utils\ObjectMapper\Decoder;


use Pingframework\Boot\Annotations\MapProperty;
use Pingframework\Boot\Utils\Arrays\ArrayKeyNotExistsException;
use Pingframework\Boot\Utils\Arrays\ArrayTypeValidationException;
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapperException;
use Pingframework\Boot\Utils\Strings\Strings;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;

/**
 * Responsible for retrieving the value from array and decoding it for specific object property.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DefaultValueDecoder extends AbstractValueDecoder
{
    /**
     * Retrieving the value from array and decoding it for specific object property.
     *
     * @param array                                  $payload
     * @param MapProperty                            $mp
     * @param ReflectionParameter|ReflectionProperty $rp
     * @return mixed
     * @throws ObjectMapperException
     */
    public function unmarshal(
        array                                  $payload,
        MapProperty                            $mp,
        ReflectionParameter|ReflectionProperty $rp
    ): mixed {
        $key = $this->getKey($mp, $rp->getName());
        $isExists = $this->arrayGetterHelper->isExists($payload, $key);
        $hasDefaultValue = $this->hasDefaultValue($rp);

        if (!$hasDefaultValue && !$isExists) {
            throw new ObjectMapperException("Failed to find field '{$key}' in the map");
        }

        if ($hasDefaultValue && !$isExists) {
            return $this->getDefaultValue($rp);
        }

        $typeName = $this->getTypeName($rp->getType());

        if ($typeName === MapProperty::TYPE_ARRAY) {
            $data = $this->cast($payload, $key, MapProperty::TYPE_ARRAY);
            $res = [];
            foreach ($data as $k => $item) {
                $res[$k] = $this->cast($data, (string)$k, $mp->getEntityType());
            }
            return $res;
        }

        return $this->cast($payload, $key, $typeName);
    }

    private function getKey(MapProperty $mp, string $propName): string
    {
        return $mp->getKey() ?? Strings::camelCaseToUnderscore($propName);
    }

    /**
     * @param ReflectionType|null $type
     * @return string
     * @throws ObjectMapperException
     */
    private function getTypeName(?ReflectionType $type): string
    {
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }

        // NOTE!!! union type is not supported

        throw new ObjectMapperException("Unsupported type");
    }

    /**
     * @param array  $payload
     * @param string $key
     * @param string $type
     * @return mixed
     * @throws ObjectMapperException
     */
    private function cast(array $payload, string $key, string $type): mixed
    {
        try {
            switch ($type) {
                case MapProperty::TYPE_INT:
                    return $this->arrayGetterHelper->mustGetInt($payload, $key);
                case MapProperty::TYPE_FLOAT:
                    return $this->arrayGetterHelper->mustGetFloat($payload, $key);
                case MapProperty::TYPE_STRING:
                    return $this->arrayGetterHelper->mustGetString($payload, $key);
                case MapProperty::TYPE_BOOL:
                    return $this->arrayGetterHelper->mustGetBool($payload, $key);
                case MapProperty::TYPE_MIXED:
                    return $this->arrayGetterHelper->mustGetMixed($payload, $key);
            }

            $data = $this->arrayGetterHelper->mustGetArray($payload, $key);

            if ($type === MapProperty::TYPE_ARRAY) {
                return $data;
            }

            return $this->objectDecoder->unmarshal($data, $type);
        } catch (ArrayKeyNotExistsException|ArrayTypeValidationException $e) {
            throw new ObjectMapperException("Failed to cast '{$type}' value for map key '{$key}'", $e->getCode(), $e);
        }
    }
}