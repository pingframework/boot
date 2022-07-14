# Ping - Boot

Ping Boot is basically an extension of the Ping framework, 
which eliminates the boilerplate configurations required for 
setting up a Ping application.

It takes an opinionated view of the Ping platform, 
which paves the way for a faster and more efficient development ecosystem.

Here are just a few of the features in Ping Boot:
- Embedded server to avoid complexity in application deployment
- Automatic config for Ping functionality – whenever possible

## Application configuration

Let's explore the configuration required to create a web application 
using both Ping and Ping Boot.

```php
#[PingBootApplication] // Marker for the application class
#[ComponentScan(['My\\Namespace'])] // scan for components in My\Namespace classes
#[ConfigFile(['/path/to/my/config.php'])] // load config from a file
class MyPingBootApplication extends AbstractPingBootApplication
{
    #[Autowired] // called by the container right after the application class is created 
    public function configure(): void
    {
        $this->getApplicationContext()->set('answer', 42); // set a configuration value (dependency container)
    }
}
```

## Services

Now let's configure the service.

```php
#[Service] // Marker for the service class
class MyService
{
    public function __construct(
        public readonly MyServiceDependency $dependency // injects automatically the dependency
    ) {}
    
    #[Autowired] // called by the container right after the service class is created 
    public function setUp(): void
    {
        // setup code
    }
}
```

## Http handler 

Ping boos is using the swoole as an embedded application server.
To handle the http requests, we need to create a handler class.
> NOTE: You can have multiple handlers in your application.

```php
#[HttpRequestHandler]
class MyHttpRequestHandler implements HttpRequestHandlerInterface
{
    public function __construct(
        public readonly MyService $myService // injects the service
    ) {}

    public function handle(Request $request, Response $response): void
    {
        $response->end("Hello world! " . $this->myService->getAnswer()); // send the response 
    }
}
```

## Run embedded "swoole" server

Ping boot provides a simple way to run the embedded server.

First we need to create am execution file.

```php
#!/usr/bin/env php
<?php

foreach ([__DIR__ . '/../../../autoload.php', __DIR__ . '/../vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

\My\Namespace\MyConsoleApplication::build()->run();
```

```bash
$ php console.php app:serve
```