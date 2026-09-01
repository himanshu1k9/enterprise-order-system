<?php

declare(strict_types = 1);

namespace App\Validation\Rules;

use Override;

class MinRule implements Rule
{
    /**
     * Defining type of minimum is Union [Either Integer or Float]
     *
     * @param integer|float $minimum
     */
    public function __construct(private int|float $minimum)
    {}

    #[Override]
    public function validate(string $field, mixed $value): ?string
    {
        /**
         * This means minimum rule doesn't care about empty value
         * it should handled by required rule.
         */
        if($value === null || $value === '') {
            return null;
        }

        /**
         * Checking if the value is number
         */
        if(is_numeric($value)) {
            if((float) $value < $this->minimum) {
                return "Minimum value is {$this->minimum}.";
            }
            return null;
        }

        /**
         * Checking if the value is string
         */
        if(is_string($value)) {
            if(mb_strlen($value) < $this->minimum) {
                return "Minimum length is {$this->minimum}.";
            }
            return null;
        }

        /**
         * Checking if the value is array
         */
        if(is_array($value)) {
            if(count($value) < $this->minimum) {
                return "Minimum items required {$this->minimum}.";
            }
            return null;
        }

        return null;
    }
}