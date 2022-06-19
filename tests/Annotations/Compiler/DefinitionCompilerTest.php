<?php

namespace Pingframework\Boot\Tests\Annotations\Compiler;

use PHPUnit\Framework\TestCase;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScanner;
use Pingframework\Boot\Annotations\Compiler\DefinitionCompiler;
use Pingframework\Boot\Annotations\ServiceDefinitionRegistrar;
use Pingframework\Boot\Annotations\Variadic;
use Pingframework\Boot\DependencyContainer\DependencyContainer;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Tests\Annotations\TestService3;
use Pingframework\Boot\Utils\DependencyContainer\DI;
use ReflectionAttribute;

class DefinitionCompilerTest extends TestCase
{

    public function testCompile()
    {
        $c = new DependencyContainer([
            'foo' => 42,
            'baz' => 'qux',
            'func' => DI::func(fn (DependencyContainerInterface $di): int => $di->get('foo')),
        ]);
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Pingframework\\Boot\\Tests\\Annotations'], 'AttributeScanner');
        foreach ($rs->get(ServiceDefinitionRegistrar::class, true) as $rc) {
            /** @var ServiceDefinitionRegistrar $ai */
            $ai = $rc->getAttributes(
                ServiceDefinitionRegistrar::class,
                ReflectionAttribute::IS_INSTANCEOF
            )[0]->newInstance();
            $ai->registerService($rs, $c, $rc);
        }
        $compiler = new DefinitionCompiler();
        $file = __DIR__ . '/../../../var/cache/definitions.php';
        $output = $compiler->compile($c, $file);

        $this->assertFileExists($file);
        $c2 = new DependencyContainer(require $file);
        $o = $c2->get(TestService3::class);
        $this->assertInstanceOf(TestService3::class, $o);
        $this->assertEquals(42, $o->getFoo());
        $this->assertEquals('qux', $o->getBaz());
        $this->assertEquals(42, $o->getZoo());
        $this->assertCount(2, $o->getVariadic());
        $this->assertEquals(42, $c2->get('func'));
    }
}
