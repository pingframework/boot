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
 * Maps json string to object's properties based on MapProperty attribute.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface JsonObjectDecoderInterface
{
    /**
     * Maps json string to object's properties based on MapProperty attribute.
     *
     * @template T
     *
     * @param string          $json
     * @param class-string<T> $class Class name.
     *
     * @return T
     *
     * @throws ObjectMapperException
     */
    public function unmarshal(string $json, string $class): object;

    /**
     * Maps json string to list of objects based on MapProperty attribute.
     *
     * @template T
     *
     * @param string          $json
     * @param class-string<T> $class Class name.
     *
     * @return array<T>
     *
     * @throws ObjectMapperException
     */
    public function unmarshalList(string $json, string $class): array;
}