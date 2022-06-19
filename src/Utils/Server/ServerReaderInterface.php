<?php

/**
 * Context
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
 * @package   beachbum\context
 * @author    Danny Damsky <danny@bbumgames.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\Utils\Server;

use Pingframework\Boot\Utils\Arrays\ArrayKeyNotExistsException;
use Pingframework\Boot\Utils\Arrays\ArrayTypeValidationException;

/**
 * Server Reader Interface
 *
 * @package   beachbum\context
 * @author    Danny Damsky <danny@bbumgames.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
interface ServerReaderInterface
{
    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetString(string $key): string;

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetInt(string $key): int;

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetFloat(string $key): float;

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetBool(string $key): bool;

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetArray(string $key): array;

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetString(string $key): ?string;

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetInt(string $key): ?int;

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetFloat(string $key): ?float;

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetBool(string $key): ?bool;

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetArray(string $key): ?array;
}
