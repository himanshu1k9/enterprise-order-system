<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

use Override;

class EmailRule implements Rule
{
    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        /**
         * EmailRule not responsible for the empty value, even this should
         * handled by RequiredRule if required.
         */
        if($value === null || $value === '') {
            return null;
        }

        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "Must be a valid email address.";
        }
        return null;
    }
}