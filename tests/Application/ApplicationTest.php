<?php

namespace Pingframework\Boot\Tests\Application;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testBuild()
    {
        $app = TestPingBootApplication1::build();
        $c = $app->getApplicationContext();

        $this->assertInstanceOf(TestPingBootApplication1::class, $c->get(TestPingBootApplication1::class));
        $this->assertEquals('bar', $c->get('foo'));
        $this->assertEquals(42, $c->get('answer'));
        $this->assertEquals(42, $c->get('answer2'));

        $this->assertEquals(['x' => 1, 'y' => 2, 'z' => 3], $c->get('arr'));
    }
}
