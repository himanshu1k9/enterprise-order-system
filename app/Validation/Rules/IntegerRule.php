<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use Override;

class IntegerRule implements Rule
{
    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        if ($value !== null && filter_var($value,FILTER_VALIDATE_INT) === false) {
            return 'Must be an integer.';
        }

        return null;
    }
}