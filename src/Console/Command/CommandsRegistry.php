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

namespace Pingframework\Boot\Console\Command;

use Pingframework\Boot\Annotations\Command;
use Pingframework\Ping\Annotations\Service;
use ReflectionAttribute;
use ReflectionObject;
use RuntimeException;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service]
class CommandsRegistry
{
    public readonly array $commands;

    public function __construct(
        AbstractCommand ...$commands
    ) {
        $this->commands = $commands;

        foreach ($commands as $command) {
            $this->configure($command);
        }
    }

    private function configure(AbstractCommand $command): void
    {
        $ca = $this->getAttribute($command);
        $command->autowiredConfigure(
            $ca->name,
            $ca->description,
            $ca->aliases,
            $ca->hidden,
        );
    }

    private function getAttribute(AbstractCommand $command): Command
    {
        $ro = new ReflectionObject($command);
        foreach ($ro->getAttributes(Command::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            return $attribute->newInstance();
        }

        throw new RuntimeException(sprintf(
            "Command attribute is missing on class %s",
            $ro->getName()
        ));
    }
}