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

use Pingframework\Boot\Annotations\ComponentScan;
use Pingframework\Boot\Annotations\ConfigFile;
use Pingframework\Ping\Annotations\Autowired;
use Pingframework\Ping\DependencyContainer\Builder\ContainerBuilder;
use Pingframework\Ping\DependencyContainer\DependencyContainerException;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use Pingframework\Ping\DependencyContainer\ServiceNotFoundException;
use Pingframework\Ping\DependencyContainer\ServiceResolveException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionObject;

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

    #[Autowired]
    public function configureFromFile(): void
    {
        foreach ($this->findConfigFiles() as $file) {
            $definitions = require $file;
            foreach ($definitions as $k => $v) {
                $this->applicationContext->set($k, $v);
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
        );

        // exclude main/current application from variadic definitions map
        $c->getAttributeScannerResultSet()->getVdm()->remove(
            ApplicationRegistry::class,
            static::class
        );

        // resolve all nested applications
        $c->get(ApplicationRegistry::class);

        // resolve main application (must be latest to make ability to override container definitions)
        return $c->get(static::class);
    }

    private function findConfigFiles(): array
    {
        $ro = new ReflectionObject($this);
        foreach ($ro->getAttributes(ConfigFile::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
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