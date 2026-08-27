<?php

declare(strict_types = 1);

namespace App;

use App\Container\Container;
use App\Http\Request;
use App\Routing\Router;

class Application
{
    public function __construct(
        private Container $container,
        private Router $router,
        private Request $request
    ) {}

    public function run(): void
    {
        $method = $this->request->method();
        $url = $this->request->url();
        $this->router->dispatch($method, $url);
    }
}