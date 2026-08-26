<?php

declare(strict_types = 1);

namespace App\Container;

class Container
{
    private array $bindings = [];
    public function set(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function get(string $abstract): mixed
    {
        if(!isset($this->bindings[$abstract])) {
            throw new \RuntimeException("No binding found for {$abstract}");
        }

        return ($this->bindings[$abstract])();
    }
}