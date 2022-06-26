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

namespace Pingframework\Boot\Logging;

use Pingframework\Ping\Annotations\Service;
use Pingframework\Ping\Utils\Json\JsonEncoderInterface;
use Psr\Log\LoggerInterface;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(LoggerInterface::class)]
class DefaultLogger implements LoggerInterface
{
    public function __construct(
        public readonly JsonEncoderInterface $jsonEncoder,
    ) {}

    /**
     * System is unusable.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->log("EMERGENCY", $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->log("ALERT", $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->log("CRITICAL", $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->log("ERROR", $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->log("WARNING", $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->log("NOTICE", $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->log("INFO", $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     */
    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->log("DEBUG", $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed              $level
     * @param string|\Stringable $message
     * @param mixed[]            $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        error_log(sprintf(
            "%s: %s. Context: %s",
            strtoupper($level),
            $message,
            $this->jsonEncoder->marshal($context)
        ));
    }
}