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

namespace Pingframework\Boot\Tests\Annotations;

use Pingframework\Boot\Annotations\Inject;
use Pingframework\Boot\Annotations\Autowire;
use Pingframework\Boot\Annotations\Service;

/**
 * @author    Oleg Bronzov <oleg.bronzov@gmail.com>
 * @copyright 2022
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */
#[Service]
class TestService3
{
    #[Inject]
    private int $foo;

    #[Inject]
    private TestService1 $testService1;

    private int   $zoo = 0;
    private array $variadic;

    public function __construct(
        private TestService2 $testService2,
        #[Inject]
        private string $baz,
        #[Inject]
        private ?string $qux = null,
        TestService1         ...$variadic
    ) {
        $this->variadic = $variadic;
    }

    /**
     * @return TestService1[]
     */
    public function getVariadic(): array
    {
        return $this->variadic;
    }

    #[Autowire]
    public function setZoo(): void
    {
        $this->zoo = 42;
    }

    /**
     * @return int
     */
    public function getZoo(): int
    {
        return $this->zoo;
    }

    /**
     * @return int
     */
    public function getFoo(): int
    {
        return $this->foo;
    }

    /**
     * @return TestService1
     */
    public function getTestService1(): TestService1
    {
        return $this->testService1;
    }

    /**
     * @return TestService2
     */
    public function getTestService2(): TestService2
    {
        return $this->testService2;
    }

    /**
     * @return string
     */
    public function getBaz(): string
    {
        return $this->baz;
    }
}