<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

use Override;

class RegexRule implements Rule
{
    public function __construct(private string $pattern)
    {}

    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        /**
         * RegexRule doesn't responsible for empty values even it should
         * validate by required field if required
         */
        if($value === null || $value === '') {
            return null;
        }

        if(!is_string($value)) {
            return "Must be a string.";
        }

        $result = preg_match($this->pattern, $value);
        if($result !== 1) {
            return "Invalid format.";
        }
        return null;
    }
}