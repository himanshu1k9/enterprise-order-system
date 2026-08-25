<?php

declare(strict_types=1);

namespace App\Services;

class GreetingService {
    public function message(): string {
        return "Hello, From GreetingService.";
    }
}