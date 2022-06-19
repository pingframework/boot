<?php

/**
 * Ping Boot
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * Json RPC://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phpsuit.net so we can send you a copy immediately.
 *
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

declare(strict_types=1);

namespace Pingframework\Boot\DependencyContainer\Definition;

use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\DependencyContainer\ServiceNotFoundException;
use Pingframework\Boot\DependencyContainer\ServiceResolveException;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class VariadicDefinition implements DefinitionInterface
{
    public readonly array $services;

    public function __construct(
        string ...$services,
    ) {
        $this->services = $services;
    }

    /**
     * @param DependencyContainerInterface $c
     * @return array
     * @throws ServiceResolveException
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     */
    public function resolve(DependencyContainerInterface $c): array
    {
        return array_map(fn(string $service): mixed => $c->get($service), $this->services);
    }

    public static function __set_state(array $array): DefinitionInterface
    {
        return new self(...$array['services']);
    }
}