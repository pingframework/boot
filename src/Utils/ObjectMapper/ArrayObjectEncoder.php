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
use Pingframework\Boot\Utils\ObjectMapper\Encoder\ValueEncoderInterface;
use Pingframework\Boot\Utils\Strings\Strings;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Throwable;

/**
 * Converts object into pure php array based on attributes.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(ArrayObjectEncoderInterface::class)]
class ArrayObjectEncoder implements ArrayObjectEncoderInterface
{
    /**
     * Converts object into pure php array.
     *
     * @param object $object
     *
     * @return array
     *
     * @throws ObjectMapperException
     */
    public function marshal(object $object): array {
        try {
            $map = [];

            $ro = new ReflectionObject($object);

            foreach ($ro->getProperties() as $rp) {
                $mp = $this->getAttr($rp);
                if (is_null($mp) || $mp->getEncoder() === null) {
                    continue;
                }

                $value = $rp->getValue($object);
                if (empty($value) && $mp->isOmitempty()) {
                    continue;
                }

                $map[$this->getKey($mp, $rp->getName())] = $this->getValueEncoder($mp)->marshal($mp, $rp, $ro, $value);
            }

            return $map;
        } catch (Throwable $e) {
            throw new ObjectMapperException("Failed to marshal object", $e->getCode(), $e);
        }
    }

    private function getKey(MapProperty $mp, string $propName): string
    {
        return $mp->getKey() ?? Strings::camelCaseToUnderscore($propName);
    }

    /**
     * Converts list of objects into pure php array.
     *
     * @param array<object> $objects
     *
     * @return array
     *
     * @throws ObjectMapperException
     */
    public function marshalList(array $objects): array
    {
        return array_map([$this, "marshal"], $objects);
    }

    /**
     * @param ReflectionProperty $property
     *
     * @return MapProperty|null
     */
    private function getAttr(ReflectionProperty $property): ?MapProperty
    {
        $attrList = $property->getAttributes(MapProperty::class, ReflectionAttribute::IS_INSTANCEOF);

        if (count($attrList) === 0) {
            return null;
        }

        return $attrList[0]->newInstance();
    }

    /**
     * @param MapProperty $mp
     * @return ValueEncoderInterface
     * @throws ObjectMapperException
     */
    private function getValueEncoder(MapProperty $mp): ValueEncoderInterface
    {
        $encoderClass = $mp->getEncoder();
        $instance = new $encoderClass($this);

        if (!$instance instanceof ValueEncoderInterface) {
            throw new ObjectMapperException("Encoder must implement ValueEncoderInterface");
        }

        return $instance;
    }
}