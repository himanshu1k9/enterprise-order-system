<?php

declare(strict_types = 1);

namespace App\Routing;

class Router {
    private array $router = [];

    // Get Routes
    public function get(string $path, callable $handler): void {
        $this->addRouter('GET', $path, $handler);
    }

    // Post routes
    public function post(string $path, callable $handler): void {
        $this->addRouter('POST', $path, $handler);
    }

    // Put routes
    public function put(string $path, callable $handler): void {
        $this->addRouter('PUT', $path, $handler);
    }

    // Patch routes
    public function patch(string $path, callable $handler): void {
        $this->addRouter('PATCH', $path, $handler);
    }

    // Delete routes
    public function delete(string $path, callable $handler): void {
        $this->addRouter('DELETE', $path, $handler);
    }

    // Helper to add routes together and build an array of routes
    private function addRouter(string $method, string $path, callable $handler): void {
        $this->router[$method][] = [
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(
        string $method,
        string $url
    ): void {
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
                $this->invokeHandler($route['handler'], $matches);
                return;
            }
        }

        foreach($this->router as $routes) {
            foreach($routes as $route) {
                $pattern = $this->convertRouteToRegex(
                    $route['path']
                );

                if (preg_match($pattern, $url)) {
                    $methodMatched = true;
                    break 2;
                }
            }
        }



        // http_response_code(404);

        // echo '404 - Page Not Found';

        if ($methodMatched) {
            http_response_code(405);
            echo '405 - Method Not Allowed';
            return;
        }

        http_response_code(404);
        echo '404 - Page Not Found';
    }

    private function invokeHandler(
        callable $handler,
        array $parameters
    ): void {
        call_user_func_array(
            $handler,
            $parameters
        );
    }

    private function convertRouteToRegex(
        string $route
    ): string {
        $pattern = preg_replace(
            '/\{([^}]+)\}/',
            '([^/]+)',
            $route
        );

        return '#^' . $pattern . '$#';
    }
}