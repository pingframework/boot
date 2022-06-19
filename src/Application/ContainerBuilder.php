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

namespace Pingframework\Boot\Application;

use Exception;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScanner;
use Pingframework\Boot\Annotations\Compiler\DefinitionCompiler;
use Pingframework\Boot\Annotations\ServiceDefinitionRegistrar;
use Pingframework\Boot\DependencyContainer\DependencyContainer;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionAttribute;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
class ContainerBuilder
{
    /**
     * Builds dependency container based on annotations.
     *
     * @throws Exception
     */
    public static function build(
        array   $namespaces,
        string  $applicationClass,
        ?string $excludeRegexp = null,
        ?string $outputFile = null,
        bool    $debug = false
    ): DependencyContainerInterface {
        // load cache if exists and debug is off
        if (!$debug && $outputFile !== null && file_exists($outputFile)) {
            return new DependencyContainer(require $outputFile);
        }

        $c = new DependencyContainer();
        $c->set('debug', DI::value($debug)); // set debug mode into container
        $scanner = new AttributeScanner();
        $compiler = new DefinitionCompiler();
        // process namespaces to scan (drop duplicates, add default namespace)
        $namespaces = self::getNamespaces($namespaces);

        // scan for classes with attributes
        $rs = $scanner->scan($namespaces, $excludeRegexp);

        // exclude main application from registry to avoid auto-resolving
        // it will be initialized later
        $rs->dropVariadic(ApplicationRegistry::class, $applicationClass);

        // register services and it's definitions
        foreach ($rs->get(ServiceDefinitionRegistrar::class, true) as $rc) {
            foreach (
                $rc->getAttributes(
                    ServiceDefinitionRegistrar::class,
                    ReflectionAttribute::IS_INSTANCEOF
                ) as $attribute
            ) {
                /** @var ServiceDefinitionRegistrar $ai */
                $ai = $attribute->newInstance();
                $ai->registerService($rs, $c, $rc);
            }
        }

        // compile definitions into cache file if debug is off and output file is set
        if (!$debug && $outputFile !== null) {
            $compiler->compile($c, $outputFile);
        }

        return $c;
    }


    private static function getNamespaces(array $namespaces): array
    {
        // add "Pingframework" namespace always to the scan list
        $namespaces[] = 'Pingframework';

        // remove duplicates
        $namespaces = array_unique($namespaces);

        // drop duplicates which starts with same namespace
        foreach ($namespaces as $ns1) {
            foreach ($namespaces as $k => $ns2) {
                if ($ns1 !== $ns2 && str_starts_with($ns2, $ns1)) {
                    unset($namespaces[$k]);
                }
            }
        }

        return $namespaces;
    }
}