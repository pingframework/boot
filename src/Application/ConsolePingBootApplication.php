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
use Pingframework\Boot\Annotations\Autowire;
use Pingframework\Boot\Annotations\PingBootApplication;
use Pingframework\Boot\Command\CommandsRegistry;
use Symfony\Component\Console\Application;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[PingBootApplication]
class ConsolePingBootApplication extends AbstractPingBootApplication
{
    private Application $symfonyApplication;

    #[Autowire]
    public function configure(
        CommandsRegistry $commandsRegistry
    ): void {
        $this->symfonyApplication = new Application();

        foreach ($commandsRegistry->commands as $command) {
            $this->symfonyApplication->add($command);
        }
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws Exception When running fails. Bypass this when {@link Application::setCatchExceptions()}.
     */
    public function run(): int
    {
        return $this->symfonyApplication->run();
    }

    /**
     * @return Application
     */
    public function getSymfonyApplication(): Application
    {
        return $this->symfonyApplication;
    }
}