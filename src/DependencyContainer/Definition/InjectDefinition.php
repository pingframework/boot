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
class InjectDefinition implements DefinitionInterface
{
    public function __construct(
        private string $containerKey,
        private bool $isOptional = false,
        private mixed $default = null,
    ) {}

    /**
     * @param DependencyContainerInterface $c
     *
     * @return mixed
     *
     * @throws DependencyContainerException
     * @throws ServiceNotFoundException
     * @throws ServiceResolveException
     */
    public function resolve(DependencyContainerInterface $c): mixed
    {
        if (!$this->isOptional && !$c->has($this->containerKey)) {
            throw new ServiceNotFoundException(sprintf(
                'Service "%s" not found in container',
                $this->containerKey
            ));
        }

        if ($this->isOptional && !$c->has($this->containerKey)) {
            return $this->default;
        }

        return $c->get($this->containerKey);
    }

    public static function __set_state(array $array): DefinitionInterface
    {
        return new self(
            $array['containerKey'],
            $array['isOptional'],
            $array['default']
        );
    }
}