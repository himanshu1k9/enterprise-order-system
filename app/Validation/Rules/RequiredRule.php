<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

use Override;

class RequiredRule implements Rule
{
    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        if($value === null || $value === '') {
            return 'This field is required.';
        }
        return null;
    }
}