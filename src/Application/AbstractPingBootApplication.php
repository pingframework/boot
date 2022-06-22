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
use Pingframework\Boot\Annotations\Inject;
use Pingframework\Boot\DependencyContainer\DependencyContainerException;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\DependencyContainer\ServiceNotFoundException;
use Pingframework\Boot\DependencyContainer\ServiceResolveException;
use Pingframework\Boot\Utils\Arrays\Arrays;
use ReflectionClass;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractPingBootApplication implements PingBootApplicationInterface
{
    #[Inject]
    protected DependencyContainerInterface $container;
    #[Inject('debug')]
    protected bool                         $isDebug = false;

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->isDebug;
    }

    /**
     * @return DependencyContainerInterface
     */
    public function getContainer(): DependencyContainerInterface
    {
        return $this->container;
    }

    public function configureFromFile(string ...$files): void
    {
        foreach ($files as $file) {
            $this->container->setDefinitions(require $file);
        }
    }

    /**
     * Main entry point.
     * Scan classes for attributes and builds dependency container based on result.
     * Resolves all nested application classes.
     * Configure each found application.
     *
     * @param bool  $debug
     * @param array $definitions
     * @return static
     *
     * @throws DependencyContainerException
     * @throws ServiceNotFoundException
     * @throws ServiceResolveException
     */
    public static function build(bool $debug = false, array $definitions = []): static
    {
        // analyze current application class for attributes
        $rc = new ReflectionClass(static::class);

        // find component scan annotation
        $cs = self::findComponentScan($rc);

        // build dependency container based on component scan annotations
        $c = ContainerBuilder::build(
            $cs->getNamespaces(),
            static::class,
            $cs->getExcludeRegexp(),
            $cs->getOutputFile(),
            $debug,
        );

        // apply all config files before resolve applications
        self::applyConfigFiles($c);

        // apply passed definitions
        $c->setDefinitions($definitions);

        // resolve all nested applications
        $c->get(ApplicationRegistry::class);

        // resolve main application (must be latest)
        return $c->get(static::class);
    }

    private static function applyConfigFiles(DependencyContainerInterface $c): void
    {
        Arrays::stream(self::sortConfigFiles($c, $c->get(ConfigFileRegistry::class)->getPaths()))->each(
            fn(string $file) => $c->setDefinitions(require $file)
        );
    }

    private static function sortConfigFiles(DependencyContainerInterface $c, array $registry): array
    {
        $l = [];
        // find ordering in variadic definition of application registry constructor (it is already sorted by attribute scanner)
        $applicationOrder = $c
            ->getDefinition(ApplicationRegistry::class)
            ->getMethods()['__construct'][0]['applications']->services;
        $applicationOrder[] = static::class;

        foreach ($applicationOrder as $pba) {
            foreach ($registry[$pba] ?? [] as $configFile) {
                if (!in_array($configFile, $l)) {
                    $l[] = $configFile;
                }
            }
        }

        return $l;
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