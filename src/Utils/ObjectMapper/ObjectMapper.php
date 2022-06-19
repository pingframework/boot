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


use Pingframework\Boot\Annotations\Service;

/**
 * Facade for ObjectMappers.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(ObjectMapperInterface::class)]
class ObjectMapper implements ObjectMapperInterface
{
    public function __construct(
        public readonly ArrayObjectMapperInterface $arrayObjectMapper,
        public readonly JsonObjectMapperInterface  $jsonObjectMapper
    ) {}

    /**
     * Converts object to pure php array based on MapProperty attribute.
     *
     * @param object $object
     *
     * @return array
     *
     * @throws ObjectMapperException
     */
    public function unmapToArray(object $object): array
    {
        return $this->arrayObjectMapper->unmap($object);
    }

    /**
     * Converts list of objects to pure php array based on MapProperty attribute.
     *
     * @param array<object> $objects
     *
     * @return array
     *
     * @throws ObjectMapperException
     */
    public function unmapListToArray(array $objects): array
    {
        return $this->arrayObjectMapper->unmapList($objects);
    }

    /**
     * Maps pure php array to object's properties based on MapProperty attribute.
     *
     * @template T
     *
     * @param array           $payload PHP array.
     * @param class-string<T> $class   Class name.
     *
     * @return T
     *
     * @throws ObjectMapperException
     */
    public function mapFromArray(array $payload, string $class): object
    {
        return $this->arrayObjectMapper->map($payload, $class);
    }

    /**
     * Maps pure php array to object's properties based on MapProperty attribute.
     *
     * @template T
     *
     * @param array           $payload PHP array.
     * @param class-string<T> $class   Class name.
     *
     * @return array<T>
     *
     * @throws ObjectMapperException
     */
    public function mapListFromArray(array $payload, string $class): array
    {
        return $this->arrayObjectMapper->mapList($payload, $class);
    }

    /**
     * Converts object to json string based on MapProperty attribute.
     *
     * @param object $object
     *
     * @return string
     *
     * @throws ObjectMapperException
     */
    public function unmapToJson(object $object): string
    {
        return $this->jsonObjectMapper->unmap($object);
    }

    /**
     * Converts list of objects to json string based on MapProperty attribute.
     *
     * @param array<object> $objects
     *
     * @return string
     *
     * @throws ObjectMapperException
     */
    public function unmapListToJson(array $objects): string
    {
        return $this->jsonObjectMapper->unmapList($objects);
    }

    /**
     * Maps json string to object's properties based on MapProperty attribute.
     *
     * @template T
     *
     * @param string          $json  JSON string.
     * @param class-string<T> $class Class name.
     *
     * @return T
     *
     * @throws ObjectMapperException
     */
    public function mapFromJson(string $json, string $class): object
    {
        return $this->jsonObjectMapper->map($json, $class);
    }

    /**
     * Maps json string to list of objects based on MapProperty attribute.
     *
     * @template T
     *
     * @param string          $json  JSON string.
     * @param class-string<T> $class Class name.
     *
     * @return array<T>
     *
     * @throws ObjectMapperException
     */
    public function mapFromJsonList(string $json, string $class): array
    {
        return $this->jsonObjectMapper->mapList($json, $class);
    }
}