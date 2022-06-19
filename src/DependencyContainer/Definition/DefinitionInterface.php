<?php

namespace Pingframework\Boot\DependencyContainer\Definition;

use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\DependencyContainer\ServiceNotFoundException;
use Pingframework\Boot\DependencyContainer\ServiceResolveException;

interface DefinitionInterface
{
    /**
     * @param DependencyContainerInterface $c
     * @return mixed
     * @throws ServiceResolveException
     * @throws ServiceNotFoundException
     * @throws DependencyContainerException
     */
    public function resolve(DependencyContainerInterface $c): mixed;

    public static function __set_state(array $array): DefinitionInterface;
}