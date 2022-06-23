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

namespace Pingframework\Boot\Utils\ObjectMapper;


use Pingframework\Boot\Annotations\MapProperty;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\Utils\Arrays\ArrayGetterHelperInterface;
use Pingframework\Boot\Utils\ObjectMapper\Decoder\ValueDecoderInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;
use Throwable;

/**
 * Maps pure php array to object's properties based on MapProperty attribute.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(ArrayObjectDecoderInterface::class)]
class ArrayObjectDecoder implements ArrayObjectDecoderInterface
{
    public function __construct(
        protected ArrayGetterHelperInterface $arrayGetterHelper,
    ) {}

    /**
     * Maps pure php array to object's properties based on MapProperty attribute.
     *
     * @template T
     *
     * @param array $payload PHP array.
     * @param class-string<T> $class Class name.
     *
     * @return T
     *
     * @throws ObjectMapperException
     */
    public function unmarshal(array $payload, string $class): object
    {
        try {
            $ref = new ReflectionClass($class);

            if ($this->hasConstructor($ref)) {
                return $this->unmarshalWithConstruct($ref, $payload);
            }

            return $this->unmarshalWithProperties($ref, $payload);
        } catch (Throwable $e) {
            throw new ObjectMapperException("Failed to unmarshal object. Reason: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ReflectionClass $ref
     *
     * @return bool
     */
    private function hasConstructor(ReflectionClass $ref): bool
    {
        return $ref->hasMethod("__construct");
    }

    /**
     * @param ReflectionClass $ref
     * @param array $payload
     * @return object
     * @throws ObjectMapperException
     */
    private function unmarshalWithProperties(ReflectionClass $ref, array $payload): object
    {
        try {
            $obj = $ref->newInstance();
        } catch (\ReflectionException $e) {
            throw new ObjectMapperException("Failed to instantiate class {$ref->getName()}", $e->getCode(), $e);
        }

        foreach ($ref->getProperties() as $rp) {
            $mp = $this->getMapProperty($rp);

            if (is_null($mp) || $mp->getDecoder() === null) {
                continue;
            }

            $rp->setValue($obj, $this->getValueDecoder($mp)->unmarshal($payload, $mp, $rp));
        }

        return $obj;
    }

    /**
     * @param ReflectionClass $ref
     * @param array $payload
     * @return object
     * @throws ObjectMapperException
     */
    private function unmarshalWithConstruct(ReflectionClass $ref, array $payload): object
    {
        $args = [];

        foreach ($ref->getConstructor()->getParameters() as $parameter) {
            $mp = $this->getMapProperty($parameter);

            if (is_null($mp) || $mp->getDecoder() === null) {
                continue;
            }

            $args[$parameter->getName()] = $this->getValueDecoder($mp)->unmarshal($payload, $mp, $parameter);
        }

        try {
            return $ref->newInstanceArgs($args);
        } catch (\ReflectionException $e) {
            throw new ObjectMapperException("Failed to instantiate object {$ref->getName()}", $e->getCode(), $e);
        }
    }

    /**
     * @param MapProperty $mapProperty
     * @return ValueDecoderInterface
     * @throws ObjectMapperException
     */
    private function getValueDecoder(MapProperty $mapProperty): ValueDecoderInterface
    {
        $decoderClass = $mapProperty->getDecoder();
        $decoderInstance = new $decoderClass(
            $this,
            $this->arrayGetterHelper
        );

        if (!$decoderInstance instanceof ValueDecoderInterface) {
            throw new ObjectMapperException(
                "Map property decoder must implement ValueDecoderInterface"
            );
        }

        return $decoderInstance;
    }

    /**
     * @param ReflectionParameter|ReflectionProperty $parameter
     *
     * @return MapProperty|null
     */
    private function getMapProperty(ReflectionParameter|ReflectionProperty $parameter): ?MapProperty
    {
        $attrList = $parameter->getAttributes(MapProperty::class, ReflectionAttribute::IS_INSTANCEOF);

        if (count($attrList) === 0) {
            return null;
        }

        return $attrList[0]->newInstance();
    }
}