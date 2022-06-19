<?php

namespace Pingframework\Boot\Annotations\Composition;

use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use ReflectionClass;
use ReflectionMethod;

trait MethodDefinitionRegistrarTrait
{
    use AutowireDefinitionFinderTrait;
    use ValueDefinitionBuilderTrait;

    /**
     * Register definition in the dependency container.
     *
     * @param AttributeScannerResultSet    $rs
     * @param DependencyContainerInterface $c
     * @param ReflectionClass              $rc
     * @param ReflectionMethod             $rm
     * @return void
     * @throws DependencyContainerException
     */
    public function registerMethod(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
    ): void {
        $this->findAutowire($c, $rc)->method($rm->getName(), $this->findArgs($rs, $c, $rc, $rm));
    }

    /**
     * @throws DependencyContainerException
     */
    private function findArgs(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
    ): array {
        $args = [];

        foreach ($rm->getParameters() as $rp) {
            $args[$rp->getName()] = $this->buildDefinitionForMethod($rs, $c, $rc, $rm, $rp);
        }

        return $args;
    }
}