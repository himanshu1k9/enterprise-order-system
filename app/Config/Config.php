<?php

declare(strict_types = 1);

namespace App\Config;

use RuntimeException;

class Config
{
    public function __construct(private array $config)
    {}

    public function get(string $key): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach($segments as $segment) {
            if(!is_array($value) || !array_key_exists($segment, $value)) {
                throw new RuntimeException("Configuration key [{$key}] not found.");
            }

            $value = $value[$segment];
        }
        return $value;
    }
}