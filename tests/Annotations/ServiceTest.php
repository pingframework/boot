<?php

namespace Pingframework\Boot\Tests\Annotations;

use PHPUnit\Framework\TestCase;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScanner;
use Pingframework\Boot\Annotations\ServiceDefinitionRegistrar;
use Pingframework\Boot\DependencyContainer\DependencyContainer;
use ReflectionAttribute;

class ServiceTest extends TestCase
{
    public function testRegister() {
        $c = new DependencyContainer([
            'foo' => 42,
            'baz' => 'qux',
        ]);
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Pingframework\\Boot\\Tests\\Annotations'], 'AttributeScanner');
        foreach ($rs->get(ServiceDefinitionRegistrar::class, true) as $rc) {
            /** @var ServiceDefinitionRegistrar $ai */
            $ai = $rc->getAttributes(ServiceDefinitionRegistrar::class, ReflectionAttribute::IS_INSTANCEOF)[0]->newInstance();
            $ai->registerService($rs, $c, $rc);
        }

        $o = $c->get(TestService3::class);
        $this->assertInstanceOf(TestService3::class, $o);
        $this->assertEquals(42, $o->getFoo());
        $this->assertEquals('qux', $o->getBaz());
        $this->assertEquals(42, $o->getZoo());
        $this->assertCount(2, $o->getVariadic());
    }
}
