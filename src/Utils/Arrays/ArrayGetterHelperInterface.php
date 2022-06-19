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


/**
 * Array getter helper.
 *
 * @package   pingframework\boot
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface ArrayGetterHelperInterface
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
    public function mustGetString(array $array, string $key): string;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return int
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetInt(array $array, string $key): int;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return float
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetFloat(array $array, string $key): float;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetBool(array $array, string $key): bool;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return array
     *
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetArray(array $array, string $key): array;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return mixed
     *
     * @throws ArrayKeyNotExistsException
     */
    public function mustGetMixed(array $array, string $key): mixed;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return mixed
     */
    public function mayGetMixed(array $array, string $key): mixed;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return string|null
     *
     * @throws ArrayTypeValidationException
     */
    public function mayGetString(array $array, string $key): ?string;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return int|null
     *
     * @throws ArrayTypeValidationException
     */
    public function mayGetInt(array $array, string $key): ?int;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return float|null
     *
     * @throws ArrayTypeValidationException
     */
    public function mayGetFloat(array $array, string $key): ?float;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool|null
     *
     * @throws ArrayTypeValidationException
     */
    public function mayGetBool(array $array, string $key): ?bool;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return array|null
     *
     * @throws ArrayTypeValidationException
     */
    public function mayGetArray(array $array, string $key): ?array;

    /**
     * @param array  $array
     * @param string $key
     *
     * @return bool
     */
    public function isExists(array $array, string $key): bool;
}