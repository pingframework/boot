<?php

/**
 * core
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
 * @author    Oleg Bronzov <oleg@bbumgames.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Pingframework\Boot\Tests\Application;

use Pingframework\Boot\Annotations\PingBootApplication;
use Pingframework\Boot\Application\AbstractPingBootApplication;
use Pingframework\Ping\Annotations\Autowired;
use Pingframework\Ping\Utils\Priority;

/**
 * @author    Oleg Bronzov <oleg@bbumgames.com>
 * @copyright 2022
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
#[PingBootApplication(Priority::LOWEST)]
class TestPingBootApplication2 extends AbstractPingBootApplication
{
    #[Autowired]
    public function configure(): void
    {
        $this->getApplicationContext()->set('answer', 2);
        $this->getApplicationContext()->set('answer2', 42);
    }
}