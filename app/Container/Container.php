<?php

declare(strict_types = 1);

namespace App\Container;

use ReflectionClass;
use RuntimeException;

class Container
{
    private array $bindings = [];
    public function set(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function get(string $abstract): mixed
    {
        // if(!isset($this->bindings[$abstract])) {
        //     throw new \RuntimeException("No binding found for {$abstract}");
        // }
        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])();
        }

        return $this->build($abstract);
    }

    /**
     * Now we are going to implement Reflection
     * ----------------------------------------
     */
    private function build(string $concreate): mixed
    {
        $reflection = new ReflectionClass($concreate);
        $constructor = $reflection->getConstructor();

        if($constructor === null) {
            return $reflection->newInstance();
        }
        // return $reflection->newInstance();
        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if($type === null) {
                throw new RuntimeException("Cannot resolve parameter: ". $parameter->getName());
            }

            // if ($type->isBuiltin()) {
            //     throw new RuntimeException("Cannot resolve primitive type '{$type->getName()}' for parameter '{$parameter->getName()}'");
            // }
            
            $dependencies[] = $this->get($type->getName());
        }
        return $reflection->newInstanceArgs($dependencies);
    }
}