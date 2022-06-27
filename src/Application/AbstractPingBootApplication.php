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

use InvalidArgumentException;
use Pingframework\Boot\Annotations\ComponentScan;
use Pingframework\Boot\Annotations\ConfigFile;
use Pingframework\Ping\DependencyContainer\AutowiredServiceRegistry;
use Pingframework\Ping\DependencyContainer\Builder\ContainerBuilder;
use Pingframework\Ping\DependencyContainer\DependencyContainerException;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use Pingframework\Ping\DependencyContainer\ServiceNotFoundException;
use Pingframework\Ping\DependencyContainer\ServiceResolveException;
use ReflectionAttribute;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractPingBootApplication implements PingBootApplicationInterface
{
    public function __construct(
        private readonly DependencyContainerInterface $applicationContext
    ) {}

    /**
     * @return DependencyContainerInterface
     */
    public function getApplicationContext(): DependencyContainerInterface
    {
        return $this->applicationContext;
    }

    private static function configureFromFiles(DependencyContainerInterface $c): void
    {
        foreach (self::findConfigFiles() as $file) {
            $definitions = require $file;
            foreach ($definitions as $k => $v) {
                $c->set($k, $v);
            }
        }
    }

    /**
     * Main entry point.
     * Scan classes for attributes and builds dependency container based on result.
     * Resolves all nested application classes.
     * Configure each found application.
     *
     * @return static
     *
     * @throws DependencyContainerException
     * @throws ServiceNotFoundException
     * @throws ServiceResolveException
     */
    public static function build(): static
    {
        // analyze current application class for attributes
        $rc = new ReflectionClass(static::class);

        // find component scan annotation
        $cs = self::findComponentScan($rc);

        // build dependency container based on component scan annotations
        $c = ContainerBuilder::build(
            $cs->namespaces,
            $cs->excludeRegexp,
            false,
        );

        // exclude main/current application from variadic definitions map
        $c->getAttributeScannerResultSet()->getVdm()->remove(
            ApplicationRegistry::class,
            static::class
        );

        // configure from files nested apps
        foreach ($c->getAttributeScannerResultSet()->getVdm()->get(ApplicationRegistry::class) as $appClass) {
            if (!is_subclass_of($appClass, AbstractPingBootApplication::class)) {
                throw new InvalidArgumentException(
                    "Application class {$appClass} must extend AbstractPingBootApplication"
                );
            }
            $appClass::configureFromFiles($c);
        }
        // configure from files main apps (must be latest)
        static::configureFromFiles($c);

        // resolve all nested applications
        $c->get(ApplicationRegistry::class);

        // resolve main application (must be latest to make ability to override container definitions)
        $app = $c->get(static::class);

        // resolve all autowired services
        $c->get(AutowiredServiceRegistry::class);

        return $app;
    }

    private static function findConfigFiles(): array
    {
        $rc = new ReflectionClass(static::class);
        foreach ($rc->getAttributes(ConfigFile::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            /** @var ConfigFile $ai */
            $ai = $attribute->newInstance();
            return $ai->paths;
        }

        return [];
    }

    private static function findComponentScan(ReflectionClass $rc): ComponentScan
    {
        $attributes = $rc->getAttributes(ComponentScan::class);
        if (count($attributes) > 0) {
            return $attributes[0]->newInstance();
        }

        return new ComponentScan(['Pingframework']);
    }
}