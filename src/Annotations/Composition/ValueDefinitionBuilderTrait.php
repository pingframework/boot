<?php

namespace Pingframework\Boot\Annotations\Composition;

use Pingframework\Boot\Annotations\AttributeScanner\AttributeScannerResultSet;
use Pingframework\Boot\Annotations\ValueDefinitionBuilder;
use Pingframework\Boot\DependencyContainer\Definition\DefinitionInterface;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

trait ValueDefinitionBuilderTrait
{
    /**
     * @param AttributeScannerResultSet    $rs
     * @param DependencyContainerInterface $c
     * @param ReflectionClass              $rc
     * @param ReflectionMethod             $rm
     * @param ReflectionParameter          $rp
     * @return DefinitionInterface
     * @throws DependencyContainerException
     */
    private function buildDefinitionForMethod(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionMethod             $rm,
        ReflectionParameter          $rp
    ): DefinitionInterface {
        // try to resolve by definition builder attribute
        $db = $this->getDefinitionBuilder($rp);
        if ($db !== null) {
            return $db->buildDefinition($rs, $c, $rc, $rp, $rm);
        }

        // try to resolve by type
        $type = $this->getType($rp, $rc);

        // default value for built-in types
        if ($type->isBuiltin()) {
            if (!$rp->isOptional()) {
                throw new DependencyContainerException(
                    sprintf(
                        'Could not resolve parameter %s in method %s::%s. Built-in type without default value.',
                        $rp->getName(),
                        $rc->getName(),
                        $rm->getName(),
                    )
                );
            }

            try {
                return DI::value($rp->getDefaultValue());
            } catch (ReflectionException $e) {
                throw new DependencyContainerException($e->getMessage(), $e->getCode(), $e);
            }
        }

        // variadic type (applicable for constructor only)
        if ($rp->isVariadic()) {
            if (!$rm->isConstructor()) {
                throw new DependencyContainerException(
                    sprintf(
                        'Variadic parameter is applicable for constructors only. %s::%s',
                        $rc->getName(),
                        $rm->getName(),
                    )
                );
            }

            return DI::variadic(...$rs->getVariadics($rc->getName()));
        }

        return DI::get($type->getName());
    }

    /**
     * @param AttributeScannerResultSet    $rs
     * @param DependencyContainerInterface $c
     * @param ReflectionClass              $rc
     * @param ReflectionProperty           $rp
     * @return DefinitionInterface
     * @throws DependencyContainerException
     */
    private function buildDefinitionForProperty(
        AttributeScannerResultSet    $rs,
        DependencyContainerInterface $c,
        ReflectionClass              $rc,
        ReflectionProperty           $rp
    ): DefinitionInterface {
        // try to resolve by definition builder attribute
        $db = $this->getDefinitionBuilder($rp);
        if ($db !== null) {
            return $db->buildDefinition($rs, $c, $rc, $rp);
        }

        $type = $this->getType($rp, $rc);

        // default value for built-in types
        if ($type->isBuiltin()) {
            if (!$rp->hasDefaultValue()) {
                throw new DependencyContainerException(
                    sprintf(
                        'Could not resolve property in class %s::%s. Built-in type without default value.',
                        $rp->getName(),
                        $rc->getName(),
                    )
                );
            }

            return DI::value($rp->getDefaultValue());
        }

        return DI::get($type->getName());
    }

    private function getDefinitionBuilder(ReflectionParameter|ReflectionProperty $rp): ?ValueDefinitionBuilder
    {
        $attrs = $rp->getAttributes(ValueDefinitionBuilder::class, ReflectionAttribute::IS_INSTANCEOF);
        if (count($attrs) > 0) {
            return $attrs[0]->newInstance();
        }

        return null;
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $rp
     * @param ReflectionClass                        $rc
     * @return ReflectionNamedType
     * @throws DependencyContainerException
     */
    private function getType(ReflectionProperty|ReflectionParameter $rp, ReflectionClass $rc): ReflectionNamedType
    {
        // try to resolve by type
        $type = $rp->getType();

        // unknown type
        if ($type === null) {
            throw new DependencyContainerException(
                sprintf(
                    'Could not resolve property in class %s::%s. Unknown type.',
                    $rc->getName(),
                    $rp->getName(),
                )
            );
        }

        // unsupported type
        if (!$type instanceof ReflectionNamedType) {
            throw new DependencyContainerException(
                sprintf(
                    'Could not resolve property in class %s::%s. Unsupported type.',
                    $rc->getName(),
                    $rp->getName(),
                )
            );
        }

        return $type;
    }
}