<?php

declare(strict_types = 1);

namespace App\Routing;

use App\Exceptions\NotFoundException;
// use App\Http\Response;

class Router
{
    private array $router = [];

    // Get Routes
    public function get(string $path, callable $handler, array $middleware = []): void 
    {
        $this->addRouter('GET', $path, $handler, $middleware);
    }

    // Post routes
    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRouter('POST', $path, $handler, $middleware);
    }

    // Put routes
    public function put(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRouter('PUT', $path, $handler, $middleware);
    }

    // Patch routes
    public function patch(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRouter('PATCH', $path, $handler, $middleware);
    }

    // Delete routes
    public function delete(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRouter('DELETE', $path, $handler, $middleware);
    }

    // Helper to add routes together and build an array of routes
    private function addRouter(string $method, string $path, callable $handler, array $middleware = []): void
    {
        $this->router[$method][] = [
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $method, string $url): RouteMatch
    {
        // $handler = $this->router[$method][$url] ?? NULL;
        // if($handler === NULL) {
        //     http_response_code(404);
        //     echo '404 - Page Not found.';
        //     return;
        // }
        // call_user_func($handler);
        $methodMatched = false;
        foreach ($this->router[$method] ?? [] as $route) {
            $pattern = $this->convertRouteToRegEx($route['path']);

            if(preg_match($pattern, $url, $matches)) {
                $methodMatched = true;
                array_shift($matches);
                return new RouteMatch(
                    handler: $route['handler'],
                    parameters: $matches,
                    middleware: $route['middleware']
                );
                // return;
            }
        }

        foreach($this->router as $routes) {
            foreach($routes as $route) {
                $pattern = $this->convertRouteToRegex($route['path']);

                if (preg_match($pattern, $url)) {
                    $methodMatched = true;
                    break 2;
                }
            }
        }



        // http_response_code(404);

        // echo '404 - Page Not Found';

        if ($methodMatched) {
            // return Response::json(
            //     [
            //         'success' => false,
            //         'message' => '405 - Method Not Allowed'
            //     ], 405
            // );
            throw new NotFoundException("405 - Method not allowed");
        }

        // http_response_code(404);
        // echo '404 - Page Not Found';
        // return Response::json(
        //     [
        //         'success' => false,
        //         'message' => '404 - Page Not Found'
        //     ], 404
        // );
        throw new NotFoundException('404 - Page not found');
    }

    // private function invokeHandler(
    //     callable $handler,
    //     array $parameters
    // ): Response {
    //     $result = call_user_func_array($handler,$parameters);

    //     if (!$result instanceof Response) {
    //         throw new \RuntimeException('Route handler must return an instance of Response.');
    //     }

    //     return $result;
    // }

    private function convertRouteToRegex(string $route): string
    {
        $pattern = preg_replace(
            '/\{([^}]+)\}/',
            '([^/]+)',
            $route
        );

        return '#^' . $pattern . '$#';
    }
}