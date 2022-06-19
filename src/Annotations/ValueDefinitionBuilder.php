<?php

namespace Pingframework\Boot\Annotations;

use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\DependencyContainer\Definition\DefinitionInterface;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

interface ValueDefinitionBuilder
{
    public function buildDefinition(
        AttributeScannerResultSet              $rs,
        DependencyContainerInterface           $c,
        ReflectionClass                        $rc,
        ReflectionParameter|ReflectionProperty $rp,
        ?ReflectionMethod                      $rm = null,
    ): DefinitionInterface;
}