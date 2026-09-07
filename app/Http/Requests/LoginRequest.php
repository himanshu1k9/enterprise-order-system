<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\DTO\LoginData;
use App\Http\Request;
use App\Validation\Rules\MaxRule;
use App\Validation\Rules\MinRule;
use App\Validation\Rules\RegexRule;
use App\Validation\Rules\RequiredRule;
use App\Validation\Rules\StringRule;
use App\Validation\Validator;

class LoginRequest
{
    private array $validated = [];
    public function __construct(private Request $request)
    {}

    public function validate(): void
    {
        $header = strtolower($this->request->header('Content-Type') ?? '');
        $data = str_starts_with($header, 'application/json') ? $this->request->json() : $this->request->all();

        $validator = new Validator($data);
        $validator->validate($this->rules());
        $this->validated = $data;
    }

    private function rules(): array
    {
        return [
            'email' => [
                new RequiredRule(),
                new StringRule(),
                new MaxRule(255),
                new RegexRule('/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
            ],
            'password' => [
                new RequiredRule(),
                new StringRule(),
                new MinRule(8),
                new MaxRule(255)
            ]
        ];
    }

    public function data(): LoginData
    {
        return LoginData::fromRequest($this->request);
    }

    public function validated(): array
    {
        return $this->validated;
    }
}