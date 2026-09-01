<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

use Override;

class MaxRule implements Rule
{
    public function __construct(private int|float $maximum)
    {}

    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        /**
         * Maximum rule doesn't care about empty value it should handled
         * by required rule if required.
         */
        if($value === null || $value === '') {
            return null;
        }

        /**
         * Checking for the integer value
         */
        if(is_numeric($value)) {
            if((float) $value > $this->maximum) {
                return "Maximum value is {$this->maximum}.";
            }
            return null;
        }

        /**
         * Checking for the string value
         */
        if(is_string($value)) {
            if(mb_strlen($value) > $this->maximum) {
                return "Maximum length is {$this->maximum}.";
            }
            return null;
        }

        /**
         * Checking for the array value
         */
        if(is_array($value)) {
            if(count($value) > $this->maximum) {
                return "Maximum items allowed {$this->maximum}.";
            }
            return null;
        }
        return null;
    }
}