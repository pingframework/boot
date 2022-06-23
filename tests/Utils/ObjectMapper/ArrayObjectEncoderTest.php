<?php

namespace Pingframework\Boot\Tests\Utils\ObjectMapper;

use Pingframework\Boot\Utils\ObjectMapper\ArrayObjectEncoder;
use PHPUnit\Framework\TestCase;

class ArrayObjectEncoderTest extends TestCase
{

    public function testMarshal() {
        $encoder = new ArrayObjectEncoder();
        $o = new TestClass();
        $o->id = 42;
        $result = $encoder->marshal($o);

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('id', $result);
    }
}
