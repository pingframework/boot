<?php

namespace Pingframework\Boot\Utils;

interface Priority
{
    public const HIGHEST = 100;
    public const HIGH    = 50;
    public const NORMAL  = 0;
    public const LOW     = -50;
    public const LOWEST  = -100;
}