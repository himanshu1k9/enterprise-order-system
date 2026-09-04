<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

use Override;

class InRule implements Rule
{
    public function __construct(private array $allowed)
    {}

    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        /**
         * InRule doesn't care about if value is null or empty even it should
         * handled by RequiredRule
         */
        if($value === null || $value === '') {
            return null;
        }

        if(!in_array($value, $this->allowed, true)) {
            return 'Must be one of ' . implode(', ', $this->allowed) . '.';
        }
        return null;
    }
}