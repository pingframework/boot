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
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service(LoggerInterface::class)]
class DefaultLogger extends AbstractLogger
{
    public const LOG_LEVEL_EMERGENCY = 1;
    public const LOG_LEVEL_ALERT     = 2;
    public const LOG_LEVEL_CRITICAL  = 4;
    public const LOG_LEVEL_ERROR     = 8;
    public const LOG_LEVEL_WARNING   = 16;
    public const LOG_LEVEL_NOTICE    = 32;
    public const LOG_LEVEL_INFO      = 64;
    public const LOG_LEVEL_DEBUG     = 128;
    public const LOG_LEVEL_ALL       = self::LOG_LEVEL_EMERGENCY | self::LOG_LEVEL_ALERT | self::LOG_LEVEL_CRITICAL | self::LOG_LEVEL_ERROR | self::LOG_LEVEL_WARNING | self::LOG_LEVEL_NOTICE | self::LOG_LEVEL_INFO | self::LOG_LEVEL_DEBUG;

    public const LOG_LEVEL_MAP = [
        LogLevel::EMERGENCY => self::LOG_LEVEL_EMERGENCY,
        LogLevel::ALERT     => self::LOG_LEVEL_ALERT,
        LogLevel::CRITICAL  => self::LOG_LEVEL_CRITICAL,
        LogLevel::ERROR     => self::LOG_LEVEL_ERROR,
        LogLevel::WARNING   => self::LOG_LEVEL_WARNING,
        LogLevel::NOTICE    => self::LOG_LEVEL_NOTICE,
        LogLevel::INFO      => self::LOG_LEVEL_INFO,
        LogLevel::DEBUG     => self::LOG_LEVEL_DEBUG,
    ];

    public int $logLevel = self::LOG_LEVEL_ALL;

    public function __construct(
        public readonly JsonEncoderInterface $jsonEncoder,
    ) {}

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
        if (!(self::LOG_LEVEL_MAP[$level] & $this->logLevel)) {
            return;
        }

        error_log(
            sprintf(
                "%s: %s. Context: %s",
                strtoupper($level),
                $message,
                $this->jsonEncoder->marshal($context)
            )
        );
    }
}