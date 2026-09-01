<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

interface Rule
{
    public function validate(string $field, mixed $value): ?string;
}