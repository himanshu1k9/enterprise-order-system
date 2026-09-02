<?php

declare(strict_types = 1);

namespace App\Container;

use ReflectionClass;
use RuntimeException;
class Container
{
    private array $bindings = [];
    private array $instances = []; // For Singleton
    private array $resolving = []; // For resolving circular dependencies

    /**
     * Function for implementing singleton
     * -----------------------------------
     */
    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function() use($abstract, $factory) {
            // if(isset($this->instances[$abstract])) {
            //     return $this->instances[$abstract];
            // }

            // $this->instances[$abstract] = $factory();
            // return $this->instances[$factory];

            if (array_key_exists($abstract, $this->instances)) {
                return $this->instances[$abstract];
            }

            $this->instances[$abstract] = $factory();

            return $this->instances[$abstract];
        };
    }

    /**
     * Implementing interface auto binds
     * ----------------------------------
     */
    public function bind(string $abstract, string $concreate): void {
        $this->bindings[$abstract] = function() use($concreate) {
            return $this->build($concreate);
        };
    }

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
    private function build(string $concrete): mixed
    {
        if(!class_exists($concrete)) {
            throw new RuntimeException("Class {$concrete} does not exist.");
        }

         /**
         * Implementing Circular dependencies
         * ------------------------------------
         */
         if(isset($this->resolving[$concrete])) {
            throw new RuntimeException("Circular dependency detected: {$concrete}");
        }
        $this->resolving[$concrete] = true;

        try
        {
            $reflection = new ReflectionClass($concrete);
            if(!$reflection->isInstantiable()) {
                throw new RuntimeException( "Class {$concrete} is not instantiable.");
            }

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
                    throw new RuntimeException("Cannot resolve parameter " . $parameter->getName() . " in {$concrete}");
                }

                // if ($type->isBuiltin()) {
                //     throw new RuntimeException("Cannot resolve primitive type '{$type->getName()}' for parameter '{$parameter->getName()}'");
                // }
                
                $dependencies[] = $this->get($type->getName());
            }
            return $reflection->newInstanceArgs($dependencies);
        } finally {
            // If not the case of circular dependency then unset it.
            unset($this->resolving[$concrete]);
        }
    }
}