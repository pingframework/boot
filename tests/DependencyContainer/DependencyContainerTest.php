<?php

namespace Pingframework\Boot\Tests\DependencyContainer;

use Laravel\SerializableClosure\SerializableClosure;
use PHPUnit\Framework\TestCase;
use Pingframework\Boot\DependencyContainer\Definition\ValueDefinition;
use Pingframework\Boot\DependencyContainer\DependencyContainer;
use Pingframework\Boot\DependencyContainer\ServiceNotFoundException;

class DependencyContainerTest extends TestCase
{
    public function testConstruct()
    {
        $c = new DependencyContainer([
            'test1'          => function () {
                return 'test';
            },
            'test2'          => 'test',
            'test3'          => [
                'test' => 'test'
            ],
            TestClass::class => new TestClass('key'),
        ]);

        $this->assertEquals('test', $c->get('test1'));
        $this->assertEquals('test', $c->get('test2'));
        $this->assertEquals(['test' => 'test'], $c->get('test3'));
        $this->assertInstanceOf(TestClass::class, $c->get(TestClass::class));
    }

    public function testSet()
    {
        $c = new DependencyContainer();
        $c->set('foo', new ValueDefinition('bar'));

        $this->assertEquals('bar', $c->get('foo'));
    }

    public function testGetDefinitions()
    {
        $c = new DependencyContainer();
        $c->set('foo', new ValueDefinition('bar'));

        $this->assertInstanceOf(ValueDefinition::class, $c->getDefinition('foo'));
        $this->assertEquals('bar', $c->getDefinition('foo')->resolve($c));

        $this->expectException(ServiceNotFoundException::class);
        $c->getDefinition('foo2');
    }

    public function testHas()
    {
        $c = new DependencyContainer([
            'baz' => new ValueDefinition('bar'),
        ]);
        $c->set('foo', new ValueDefinition('bar'));

        $this->assertTrue($c->has('foo'));
    }

    public function testGet()
    {
        $c = new DependencyContainer();
        $c->set('foo', new ValueDefinition('bar'));

        $this->assertEquals('bar', $c->get('foo'));
    }
}
