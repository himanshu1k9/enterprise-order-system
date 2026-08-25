<?php

declare(strict_types = 1);

namespace App\Routing;

class Router {
    private array $router = [];

    public function get(string $path, callable $handler): void {
        $this->router['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void {
        $this->router['POST'][$path] = $handler;
    }

    public function dispatch(
        string $method,
        string $url
    ): void {
        $handler = $this->router[$method][$url] ?? NULL;
        if($handler === NULL) {
            http_response_code(404);
            echo '404 - Page Not found.';
            return;
        }
        call_user_func($handler);
    }
}