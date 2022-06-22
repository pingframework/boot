<?php

namespace Pingframework\Boot\Application;

use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;

interface PingBootApplicationInterface extends FileConfigurable
{
    public static function build(bool $debug = false, array $definitions = []): static;
    public function getContainer(): DependencyContainerInterface;
    public function isDebug(): bool;
}