<?php

namespace Pingframework\Boot\Tests\Application;

use PHPUnit\Framework\TestCase;
use Pingframework\Boot\Application\SlimPingBootApplication;
use Pingframework\Boot\Application\SwoolePingBootApplication;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\UploadedFile;

class ApplicationTest extends TestCase
{

    public function testBuild()
    {
        $app = TestPingBootApplication::build(true);
        $c = $app->getContainer();

        $this->assertInstanceOf(TestPingBootApplication::class, $c->get(TestPingBootApplication::class));
        $this->assertEquals('bar', $c->get('foo'));
    }

    public function testControllerAction1()
    {
        $app = SlimPingBootApplication::build(true);
        $app->configure();
        $c = $app->getContainer();

        $this->assertInstanceOf(SlimPingBootApplication::class, $c->get(SlimPingBootApplication::class));

        $rf = new RequestFactory();
        $response = $app->getSlimApp()->handle($rf->createRequest('GET', '/group/test/41?id2=42'));

        $this->assertEquals('TEST', (string)$response->getBody());
    }

    public function testControllerAction2()
    {
        $app = SlimPingBootApplication::build(true);
        $app->configure();
        $c = $app->getContainer();

        $this->assertInstanceOf(SlimPingBootApplication::class, $c->get(SlimPingBootApplication::class));

        $_POST['id1'] = 42;
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/group/test2';
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['slim.files'] = [
            'field1' => new UploadedFile(
                __DIR__ . '/file1.txt',
                'filename1.txt',
                'text/plain',
                filesize(__DIR__ . '/file1.txt')
            ),
            'field2' => new UploadedFile(
                __DIR__ . '/file2.txt',
                'filename2.txt',
                'text/plain',
                filesize(__DIR__ . '/file2.txt')
            ),
        ];
        $_SERVER['HTTP_X_FOO'] = 'bar';

        ob_start();
        $app->run();
        $response = ob_get_clean();

        $this->assertEquals('42', $response);
    }

    public function testControllerAction3()
    {
        $app = SlimPingBootApplication::build(true);
        $app->configure();
        $c = $app->getContainer();

        $this->assertInstanceOf(SlimPingBootApplication::class, $c->get(SlimPingBootApplication::class));

        $rf = new RequestFactory();
        $request = $rf->createRequest('POST', '/group/test3');
        $request->getBody()->write('{"id1":42}');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $app->getSlimApp()->handle($request);

        $this->assertEquals('{"id1":42}', (string)$response->getBody());
    }
}
