<?php

declare(strict_types = 1);

namespace App\Http;

use App\Container\Container;
use App\Exceptions\ExceptionHandler;
use App\Routing\Router;

class Kernel
{
    public function __construct(
        private Container $container,
        private Router $router,
        private ExceptionHandler $exceptionHandler
        )
    {}

    // public function handle(Request $request, array $middleware): Response
    // {
    //     // Starting backword of Request lifecycle
    //     $destination = function(Request $request): Response {
    //         return $this->router->dispatch($request->method(), $request->url());
    //         // return new Response();
    //     };

    //     $pipeline = $destination;
    //     foreach(array_reverse($middleware) as $middlewareClass) {
    //         $pipeline = function(Request $request) use($middlewareClass, $pipeline): Response {
    //             $middleware = $this->container->get($middlewareClass);
    //             return $middleware->handle($request, $pipeline);
    //         };
    //     }
    //     // return $pipeline($request);

    //     /**
    //      * Instead of all controllers we implemented exception globally
    //      */
    //     try
    //     {
    //         return $pipeline($request);
    //     } catch(\Throwable $e) {
    //         return $this->exceptionHandler->handle($e);
    //     }
    // }

    public function handle(Request $request, array $middleware): Response {
        $route = $this->router->dispatch($request->method(),$request->url());

        /*
        * Final destination = Controller
        */
        $pipeline = function(Request $request) use ($route): Response {
            $result = call_user_func_array($route->handler,$route->parameters);
            if (!$result instanceof Response) {
                throw new \RuntimeException(
                    'Route handler must return an instance of Response.'
                );
            }
            return $result;
        };

        /*
        * Route-specific middleware
        */
        foreach (array_reverse($route->middleware) as $middlewareClass) {
            $pipeline = function(Request $request) use ($middlewareClass, $pipeline): Response {
                $middleware = $this->container->get($middlewareClass);
                return $middleware->handle($request, $pipeline);
            };
        }

        /*
        * Global middleware
        */
        foreach (array_reverse($middleware) as $middlewareClass) {
            $pipeline = function(Request $request) use ($middlewareClass, $pipeline): Response {
                $middleware = $this->container->get($middlewareClass);
                return $middleware->handle($request, $pipeline);
            };
        }
        try {
            return $pipeline($request);
        } catch (\Throwable $e) {
            return $this->exceptionHandler->handle($e);
        }
    }
}