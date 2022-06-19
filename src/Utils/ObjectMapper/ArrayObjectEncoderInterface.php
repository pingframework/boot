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


/**
 * Converts object into pure php array based on attributes.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface ArrayObjectEncoderInterface
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
    public function marshal(object $object): array;

    /**
     * Converts list of objects into pure php array.
     *
     * @param array<object> $objects
     *
     * @return array
     *
     * @throws ObjectMapperException
     */
    public function marshalList(array $objects): array;
}