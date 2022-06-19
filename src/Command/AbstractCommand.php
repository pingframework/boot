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

namespace Pingframework\Boot\Command;

use Symfony\Component\Console\Command\Command;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
abstract class AbstractCommand extends Command implements AutowireConfigurable
{
    public function autowireConfigure(
        string  $name,
        ?string $description = null,
        array   $aliases = [],
        bool    $hidden = false,
    ): void {
        $this->setName($name);

        if ($description !== null) {
            $this->setDescription($description);
        }

        if (!empty($aliases)) {
            $this->setAliases($aliases);
        }

        $this->setHidden($hidden);
    }
}