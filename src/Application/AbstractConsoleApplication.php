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

use Pingframework\Boot\Console\Command\CommandsRegistry;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use Symfony\Component\Console\Application;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractConsoleApplication extends AbstractPingBootApplication
{
    public function __construct(
        DependencyContainerInterface     $applicationContext,
        public readonly CommandsRegistry $commandsRegistry,
        public readonly Application      $symfonyApplication,
    ) {
        parent::__construct($applicationContext);
    }

    public function run(): int
    {
        if ($this->symfonyApplication->getName() === 'UNKNOWN') {
            $this->symfonyApplication->setName('Ping Boot CLI');
        }

        foreach ($this->commandsRegistry->commands as $command) {
            $this->symfonyApplication->add($command);
        }

        return $this->symfonyApplication->run();
    }
}