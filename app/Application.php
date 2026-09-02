<?php

declare(strict_types = 1);

namespace App;

use App\Container\Container;
use App\Http\Kernel;
// use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\LoggingMiddleware;
use App\Http\Request;
use App\Http\RequestId;
// use App\Http\Response;
// use App\Routing\Router;

class Application
{
    public function __construct(
        private Container $container,
        // private Router $router,
        private Kernel $kernel,
        private Request $request,
        private RequestId $reqId
    ) {}

    public function run(): void
    {
        // $method = $this->request->method();
        // $url = $this->request->url();
        // $this->router->dispatch($method, $url);
        $response = $this->kernel->handle($this->request, [
            LoggingMiddleware::class,
            // AuthMiddleware::class
        ]);

        /**
         * Sending unique requestid with every response.
         */
        $response->withHeader('X-Request-ID', $this->reqId->get());
        $response->send(); // Calling send response method here finaly send it to browser.
    }
}