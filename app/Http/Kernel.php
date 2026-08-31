<?php

declare(strict_types = 1);

namespace App\Http;

use App\Container\Container;
use App\Routing\Router;

class Kernel
{
    public function __construct(private Container $container, private Router $router)
    {}

    public function handle(Request $request, array $middleware): Response
    {
        // Starting backword of Request lifecycle
        $destination = function(Request $request): Response {
            $this->router->dispatch($request->method(), $request->url());
            return new Response();
        };

        $pipeline = $destination;
        foreach(array_reverse($middleware) as $middlewareClass) {
            $pipeline = function(Request $request) use($middlewareClass, $pipeline): Response {
                $middleware = $this->container->get($middlewareClass);
                return $middleware->handle($request, $pipeline);
            };
        }
        return $pipeline($request);
    }
}