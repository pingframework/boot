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

namespace Pingframework\Boot\Utils\Arrays;


use Pingframework\Boot\Annotations\Service;

/**
 * Array getter helper.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(ArrayGetterHelperInterface::class)]
class ArrayGetterHelper implements ArrayGetterHelperInterface
{
    public function mustGetString(array $array, string $key): string
    {
        return Arrays::mustGetString($array, $key);
    }

    public function mustGetInt(array $array, string $key): int
    {
        return Arrays::mustGetInt($array, $key);
    }

    public function mustGetFloat(array $array, string $key): float
    {
        return Arrays::mustGetFloat($array, $key);
    }

    public function mustGetBool(array $array, string $key): bool
    {
        return Arrays::mustGetBool($array, $key);
    }

    public function mustGetArray(array $array, string $key): array
    {
        return Arrays::mustGetArray($array, $key);
    }

    public function mustGetMixed(array $array, string $key): mixed
    {
        return Arrays::mustGetMixed($array, $key);
    }

    public function mayGetMixed(array $array, string $key): mixed
    {
        return Arrays::mayGetMixed($array, $key);
    }

    public function mayGetString(array $array, string $key): ?string
    {
        return Arrays::mayGetString($array, $key);
    }

    public function mayGetInt(array $array, string $key): ?int
    {
        return Arrays::mayGetInt($array, $key);
    }

    public function mayGetFloat(array $array, string $key): ?float
    {
        return Arrays::mayGetFloat($array, $key);
    }

    public function mayGetBool(array $array, string $key): ?bool
    {
        return Arrays::mayGetBool($array, $key);
    }

    public function mayGetArray(array $array, string $key): ?array
    {
        return Arrays::mayGetArray($array, $key);
    }

    public function isExists(array $array, string $key): bool
    {
        return Arrays::isExists($array, $key);
    }
}