<?php

namespace Pingframework\Boot\Annotations\Composition;

use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use ReflectionClass;
use ReflectionProperty;

trait PropertyDefinitionRegistrarTrait
{
    use AutowireDefinitionFinderTrait;
    use ValueDefinitionBuilderTrait;

    /**
     * Register definition in the dependency container.
     *
     * @param AttributeScannerResultSet    $rs
     * @param DependencyContainerInterface $c
     * @param ReflectionClass              $rc
     * @param ReflectionProperty           $rp
     *
     * @return void
     *
     * @throws DependencyContainerException
     */
    public function registerProperty(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionProperty           $rp,
    ): void {
        $this->findAutowire($c, $rc)->property($rp->getName(), $this->buildDefinitionForProperty($rs, $c, $rc, $rp));
    }
}