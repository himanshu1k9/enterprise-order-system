<?php

declare(strict_types=1);

namespace App\Routing;

class RouteMatch
{
    public function __construct(
        public readonly mixed $handler,
        public readonly array $parameters,
        public readonly array $middleware
    ) {}
}