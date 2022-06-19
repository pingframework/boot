<?php

namespace Pingframework\Boot\Annotations\Composition;

use Pingframework\Boot\DependencyContainer\Definition\AutowireDefinition;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionClass;

trait AutowireDefinitionFinderTrait
{
    public function findAutowire(
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
    ): AutowireDefinition {
        if (!$c->has($rc->getName())) {
            $c->set($rc->getName(), DI::autowire($rc->getName()));
        }
        
        $autowire = $c->getDefinition($rc->getName());

        if (!$autowire instanceof AutowireDefinition) {
            throw new DependencyContainerException(
                sprintf(
                    'Service must be registered with autowire definition only, get definition %s of class %s',
                    $autowire::class,
                    $rc->getName(),
                )
            );
        }

        return $autowire;
    }
}