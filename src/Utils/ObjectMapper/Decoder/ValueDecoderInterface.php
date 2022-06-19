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
use Pingframework\Boot\Utils\ObjectMapper\ObjectMapperException;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Responsible for retrieving value from array and decoding it for specific object property.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface ValueDecoderInterface
{
    /**
     * Retrieving value from array and decoding it for specific object property.
     *
     * @param array $payload
     * @param MapProperty $mp
     * @param ReflectionParameter|ReflectionProperty $rp
     * @return mixed
     * @throws ObjectMapperException
     */
    public function unmarshal(
        array                                  $payload,
        MapProperty                            $mp,
        ReflectionParameter|ReflectionProperty $rp
    ): mixed;
}