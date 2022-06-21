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

namespace Pingframework\Boot\DependencyContainer;

use Closure;
use Pingframework\Boot\DependencyContainer\Definition\CallbackDefinition;
use Pingframework\Boot\DependencyContainer\Definition\DefinitionInterface;
use Pingframework\Boot\DependencyContainer\Definition\ValueDefinition;
use Psr\Container\ContainerInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class DependencyContainer implements DependencyContainerInterface
{
    /**
     * @var array<string, DefinitionInterface>
     */
    private array $definitions = [];

    /**
     * @var array<string, mixed>
     */
    private array $resolved = [];

    public function __construct(array $definitions = [])
    {
        $this->resolved[static::class] = $this;
        $this->resolved[DependencyContainer::class] = $this;
        $this->resolved[ContainerInterface::class] = $this;
        $this->resolved[DependencyContainerInterface::class] = $this;
        $this->setDefinitions($definitions);
    }

    /**
     * Sets all passed definitions.
     *
     * @param array $definitions
     *
     * @return void
     */
    public function setDefinitions(array $definitions): void
    {
        foreach ($definitions as $k => $definition) {
            if ($definition instanceof DefinitionInterface) {
                $this->set($k, $definition);
            } elseif ($definition instanceof Closure) {
                $this->set($k, new CallbackDefinition($definition));
            } elseif (is_object($definition)) {
                $this->resolved[$k] = $definition;
            } elseif (is_array($definition) && isset($this->definitions[$k]) && $this->definitions[$k] instanceof ValueDefinition && is_array($this->definitions[$k]->value)) {
                $this->set($k, new ValueDefinition(array_merge($this->definitions[$k]->value, $definition)));
            } else {
                $this->set($k, new ValueDefinition($definition));
            }
        }
    }

    /**
     * Push service definition into dependency container.
     * Replaces already existing service definition if any.
     *
     * @param string              $id
     * @param DefinitionInterface $definition
     *
     * @return static
     */
    public function set(string $id, DefinitionInterface $definition): static
    {
        $this->definitions[$id] = $definition;
        return $this;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @template T
     *
     * @param class-string<T> $id Identifier of the entry to look for.
     *
     * @return T Entry.
     *
     * @throws ServiceResolveException
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     */
    public function get($id): mixed
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $this->resolved[$id] = $this->getDefinition($id)->resolve($this);
        return $this->resolved[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool
    {
        return isset($this->definitions[$id]) || isset($this->resolved[$id]);
    }

    /**
     * Returns all known service definitions.
     *
     * @return array<string, DefinitionInterface>
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * Returns service definition by its identifier.
     * Throws an exception if no service definition found.
     *
     * @param string $id
     * @return DefinitionInterface
     * @throws ServiceNotFoundException
     */
    public function getDefinition(string $id): DefinitionInterface
    {
        if (!isset($this->getDefinitions()[$id])) {
            throw new ServiceNotFoundException("Service [$id] not found");
        }

        return $this->definitions[$id];
    }
}