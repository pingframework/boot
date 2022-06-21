<?php

namespace Pingframework\Boot\Tests\Application;

use PHPUnit\Framework\TestCase;
use Pingframework\Boot\Application\JrpcSwoolePingBootApplication;
use Pingframework\Boot\DependencyContainer\DependencyContainerInterface;
use Pingframework\Boot\Http\Server\JrpcSwooleHttpRequestHandler;
use Pingframework\Boot\Tests\Swoole\Http\RequestMock;
use Pingframework\Boot\Tests\Swoole\Http\ResponseMock;

class JrpcApplicationTest extends TestCase
{

    public function testCall()
    {
        $app = JrpcSwoolePingBootApplication::build(true);
        $c = $app->getContainer();

        $json = $this->callJrpcMethod($c, 'test.test', [
            'user_id' => 42,
            'nickname' => 'test',
            'arr' => [1,2,3],
        ]);

        $this->assertIsString($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
        $this->assertCount(3, $data);
        $this->assertArrayHasKey('jsonrpc', $data);
        $this->assertArrayHasKey('result', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('2.0', $data['jsonrpc']);
        $this->assertEquals(42, $data['id']);
        $this->assertArrayHasKey('user_id', $data['result']);
        $this->assertArrayHasKey('nickname', $data['result']);
        $this->assertArrayHasKey('arr', $data['result']);
        $this->assertEquals(42, $data['result']['user_id']);
        $this->assertEquals('test', $data['result']['nickname']);
        $this->assertEquals([1,2,3], $data['result']['arr']);
    }

    public function callJrpcMethod(DependencyContainerInterface $c, string $method, array $payload = []): string
    {
        $request = new RequestMock();
        $request->server['request_uri'] = '/jrpc';
        $request->header['accept'] = 'application/json';
        $request->header['content-type'] = 'application/json';
        $request->body = json_encode([
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => $payload,
            'id'      => 42
        ]);

        $response = new ResponseMock();

        $c->get(JrpcSwooleHttpRequestHandler::class)->handle($request, $response);
        return $response->body;
    }
}
