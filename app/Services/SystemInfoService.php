<?php

namespace App\Services;

class SystemInfoService {
    public function getApplicationName(): string {
        return $_ENV['APP_NAME'] ?? $_SERVER['APP_NEM'] ?? 'Not Found';
    }

    public function getEnvironment(): string {
        return $_ENV['APP_ENV'];
    }
}