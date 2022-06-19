<?php

namespace Pingframework\Boot\Tests\Annotations\AttributeScanner;

use PHPUnit\Framework\TestCase;
use Pingframework\Boot\Annotations\AttributeScanner\AttributeScanner;
use Pingframework\Boot\Annotations\Service;
use Pingframework\Boot\Tests\Annotations\AttributeScanner\Mock\TestService;

class AttributeScannerTest extends TestCase
{
    public function testScanSingleNamespace()
    {
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Pingframework\\Boot\\Tests\\Annotations\\AttributeScanner\\Mock']);

        $this->assertCount(1, $rs->getClasses());
        $this->assertArrayHasKey(TestService::class, $rs->getClasses());
    }

    public function testScanExclude() {
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Ping'], 'Tests|Utils');

        foreach ($rs->getClasses() as $rc) {
            $this->assertStringNotContainsString('Tests', $rc->getName());
            $this->assertStringNotContainsString('Utils', $rc->getName());
        }
    }

    public function testScanMultiNamespace()
    {
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Ping']);

        foreach ($rs->getClasses() as $rc) {
            $this->assertStringStartsWith('Pingframework\\', $rc->getName());
        }
    }

    public function testResultSetIsInstanceOf()
    {
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Pingframework\\Boot\\Tests\\Annotations\\AttributeScanner\\Mock']);

        $this->assertCount(1, $rs->getClasses());
        $this->assertArrayHasKey(TestService::class, $rs->getClasses());

        $this->assertIsArray($rs->get(Service::class, true));
        $this->assertCount(1, $rs->get(Service::class, true));
        $this->assertEquals(TestService::class, $rs->get(Service::class, true)[0]->getName());
    }

    public function testResultSetVariadic()
    {
        $scanner = new AttributeScanner();
        $rs = $scanner->scan(['Pingframework\\Boot\\Tests\\Annotations\\AttributeScanner\\Mock']);

        $this->assertCount(1, $rs->getClasses());
        $this->assertArrayHasKey(TestService::class, $rs->getClasses());

        $this->assertIsArray($rs->getVariadics('testService1'));
        $this->assertIsArray($rs->getVariadics('testService2'));
        $this->assertCount(1, $rs->getVariadics('testService1'));
        $this->assertCount(1, $rs->getVariadics('testService2'));
    }
}
