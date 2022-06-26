<?php

namespace Pingframework\Boot\Application;

use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;

interface PingBootApplicationInterface
{
    public static function build(): static;
    public function getApplicationContext(): DependencyContainerInterface;
}