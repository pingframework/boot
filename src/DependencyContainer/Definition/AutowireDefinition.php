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
use ReflectionException;
use ReflectionProperty;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class AutowireDefinition implements DefinitionInterface
{
    public const CONSTRUCT_METHOD_NAME = '__construct';

    /**
     * @var array<string, array<int, array<DefinitionInterface>>>
     */
    private array $methods = [];

    /**
     * @var array<string, DefinitionInterface>
     */
    private array $properties = [];

    public function __construct(
        public readonly string $className,
    ) {}

    /**
     * @param DependencyContainerInterface $c
     *
     * @return mixed
     *
     * @throws ServiceResolveException
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     */
    public function resolve(DependencyContainerInterface $c): mixed
    {
        $ma = $this->getMethodArgs(self::CONSTRUCT_METHOD_NAME, 0, $c);
        $instance = new $this->className(...$ma[0], ...$ma[1]);
        $this->injectProperties($instance, $c);

        foreach ($this->methods as $methodName => $methodDefinitions) {
            foreach ($methodDefinitions as $i => $args) {
                if ($methodName === self::CONSTRUCT_METHOD_NAME) {
                    continue;
                }

                $this->call($c, $instance, $methodName, $i);
            }
        }

        return $instance;
    }

    /**
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     * @throws ServiceResolveException
     */
    public function call(
        DependencyContainerInterface $c,
        object                       $instance,
        string                       $method,
        int                          $index = 0,
        array                        $extraArgs = [],
        array                        $extraVariadics = []
    ): mixed {
        $ma = $this->getMethodArgs($method, $index, $c);
        return $instance->{$method}(...array_merge($ma[0], $extraArgs), ...array_merge($ma[1], $extraVariadics));
    }

    /**
     * @throws ServiceNotFoundException
     * @throws ServiceResolveException
     * @throws DependencyContainerException
     */
    private function injectProperties(object $instance, DependencyContainerInterface $c): void
    {
        foreach ($this->properties as $propertyName => $definition) {
            try {
                $rp = new ReflectionProperty($instance::class, $propertyName);
                $rp->setValue($instance, $definition->resolve($c));
            } catch (ReflectionException $e) {
                throw new ServiceResolveException(
                    sprintf("Failed to resolve property %s::%s", $instance::class, $propertyName),
                    $e->getCode(),
                    $e
                );
            }
        }
    }

    /**
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     * @throws ServiceResolveException
     */
    private function getMethodArgs(string $methodName, int $i, DependencyContainerInterface $c): array
    {
        $args = [];
        $variadic = [];

        foreach ($this->methods[$methodName][$i] ?? [] as $k => $arg) {
            if ($arg instanceof VariadicDefinition) {
                $variadic = $arg->resolve($c);
            } elseif ($arg instanceof DefinitionInterface) {
                $args[$k] = $arg->resolve($c);
            } else {
                $args[$k] = $arg;
            }
        }

        return [$args, $variadic];
    }

    /**
     * @param string                             $methodName
     * @param array<string, DefinitionInterface> $args
     * @return static
     */
    public function method(string $methodName, array $args): static
    {
        if (!isset($this->methods[$methodName])) {
            $this->methods[$methodName] = [];
        }

        $i = count($this->methods[$methodName]);
        $this->methods[$methodName][$i] = [];

        foreach ($args as $k => $v) {
            $this->setMethodArg($methodName, $i, $k, $v);
        }

        return $this;
    }

    /**
     * @param array<string, DefinitionInterface> $args
     * @return static
     */
    public function construct(array $args): static
    {
        foreach ($args as $k => $v) {
            $this->setMethodArg(self::CONSTRUCT_METHOD_NAME, 0, $k, $v);
        }

        return $this;
    }

    private function setMethodArg(string $methodName, int $i, string|int $argName, DefinitionInterface $arg): void
    {
        $this->methods[$methodName][$i][$argName] = $arg;
    }

    /**
     * @param string              $propertyName
     * @param DefinitionInterface $definition
     * @return static
     */
    public function property(string $propertyName, DefinitionInterface $definition): static
    {
        $this->properties[$propertyName] = $definition;
        return $this;
    }

    /**
     * @return array<string, array<int, array<DefinitionInterface>>>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array<string, DefinitionInterface>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public static function __set_state(array $array): DefinitionInterface
    {
        $o = new self($array['className']);
        $o->methods = $array['methods'];
        $o->properties = $array['properties'];

        return $o;
    }
}