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

declare (strict_types=1);

namespace Pingframework\Boot\Utils\Server;

use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\Utils\Arrays\ArrayGetterHelperInterface;
use Pingframework\Boot\Utils\Arrays\ArrayKeyNotExistsException;
use Pingframework\Boot\Utils\Arrays\ArrayTypeValidationException;

/**
 * Server Reader
 *
 * @package   beachbum\context
 * @author    Danny Damsky <danny@bbumgames.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(ServerReaderInterface::class)]
class ServerReader implements ServerReaderInterface
{
    private ArrayGetterHelperInterface $arrayGetterHelper;

    public function __construct(ArrayGetterHelperInterface $arrayGetterHelper)
    {
        $this->arrayGetterHelper = $arrayGetterHelper;
    }

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetString(string $key): string
    {
        return $this->arrayGetterHelper->mustGetString($_SERVER, $key);
    }

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetInt(string $key): int
    {
        return $this->arrayGetterHelper->mustGetInt($_SERVER, $key);
    }

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetFloat(string $key): float
    {
        return $this->arrayGetterHelper->mustGetFloat($_SERVER, $key);
    }

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetBool(string $key): bool
    {
        return $this->arrayGetterHelper->mustGetBool($_SERVER, $key);
    }

    /**
     * @throws ArrayKeyNotExistsException
     * @throws ArrayTypeValidationException
     */
    public function mustGetArray(string $key): array
    {
        return $this->arrayGetterHelper->mustGetArray($_SERVER, $key);
    }

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetString(string $key): ?string
    {
        return $this->arrayGetterHelper->mayGetString($_SERVER, $key);
    }

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetInt(string $key): ?int
    {
        return $this->arrayGetterHelper->mayGetInt($_SERVER, $key);
    }

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetFloat(string $key): ?float
    {
        return $this->arrayGetterHelper->mayGetFloat($_SERVER, $key);
    }

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetBool(string $key): ?bool
    {
        return $this->arrayGetterHelper->mayGetBool($_SERVER, $key);
    }

    /**
     * @throws ArrayTypeValidationException
     */
    public function mayGetArray(string $key): ?array
    {
        return $this->arrayGetterHelper->mayGetArray($_SERVER, $key);
    }
}
