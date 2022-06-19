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
use Pingframework\Boot\Utils\Json\JsonEncoder;
use JsonException;

/**
 * Converts object into json string based on attributes.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(JsonObjectEncoderInterface::class)]
class JsonObjectEncoder implements JsonObjectEncoderInterface
{
    public function __construct(
        private JsonEncoder                 $jsonEncoder,
        private ArrayObjectEncoderInterface $objectEncoder,
    ) {}

    /**
     * Converts object into json string.
     *
     * @param object $object
     *
     * @return string
     *
     * @throws ObjectMapperException
     */
    public function marshal(object $object): string
    {
        try {
            return $this->jsonEncoder->marshal($this->objectEncoder->marshal($object));
        } catch (JsonException $e) {
            throw new ObjectMapperException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Converts list of objects into json string.
     *
     * @param array<object> $objects
     *
     * @return string
     *
     * @throws ObjectMapperException
     */
    public function marshalList(array $objects): string
    {
        try {
            return $this->jsonEncoder->marshal($this->objectEncoder->marshalList($objects));
        } catch (JsonException $e) {
            throw new ObjectMapperException($e->getMessage(), $e->getCode(), $e);
        }
    }
}