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

namespace Pingframework\Boot\Event;

use League\Event\EventDispatchingListenerRegistry;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\Utils\Priority;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(EventDispatcherInterface::class, EventDispatchingListenerRegistry::class)]
class EventDispatcher extends \League\Event\EventDispatcher
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly LoggerInterface    $logger,
        ?ListenerProviderInterface          $listenerProvider = null,
    ) {
        parent::__construct($listenerProvider);
    }

    public function subscribe(
        string $eventClass,
        string $listenerClass,
        string $listenerMethod,
        int    $priority = Priority::NORMAL
    ): void {
        $this->subscribeTo(
            $eventClass,
            function (object $event) use ($listenerClass, $listenerMethod) {
                $this->logger->debug(sprintf('Event dispatched: %s::%s', $listenerClass, $listenerMethod));
                return call_user_func([$this->container->get($listenerClass), $listenerMethod], $event);
            },
            $priority
        );
    }
}