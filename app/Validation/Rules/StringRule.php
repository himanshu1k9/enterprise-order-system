<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use Override;

class StringRule implements Rule
{
    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && !is_string($value)) {
            return 'Must be a string.';
        }

        return null;
    }
}