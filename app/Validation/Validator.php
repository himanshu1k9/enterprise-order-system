<?php

declare(strict_types = 1);

namespace App\Validation;

use App\Exceptions\ValidationException;

class Validator
{
    private array $errors = [];
    public function __construct(private array $data)
    {
    }

    public function validate(array $rules): void
    {
        foreach($rules as $field => $fieldRules) {
            foreach($fieldRules as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        if($this->errors !== []) {
            throw new ValidationException($this->errors);
        }
    }

    private function applyRule(string $field, string $rule): void
    {
        [$ruleName, $parameter] = array_pad(explode(':', $rule, 2), 2, null);
        $value = $this->data[$field] ?? NULL;
        switch($ruleName) {
            case 'required':
                if($value === null || $value === '') {
                    $this->addError($field, 'This field is required.');
                }
                break;
            case 'string':
                if($value !== null && !is_string($value)) {
                    $this->addError($field, 'Must be a string.');
                }
                break;
            case 'numeric':
                if($value !== null && !is_numeric($value)) {
                    $this->addError($field, 'Must be numeric.');
                }
                break;
            case 'integer':
                if($value !== null && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, 'Must be an integer.');
                }
                break;
            case 'min':
                if($value !== null && is_numeric($value) && $value < (float) $parameter) {
                    $this->addError($field, "Minimum value is {$parameter}.");
                }
                break;
            case 'max':
                if($value !== null && is_numeric($value) && $value > (float) $parameter) {
                    $this->addError($field, "Maximum value is {$parameter}.");
                }
                break;
        }
    }

    private function AddError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
}