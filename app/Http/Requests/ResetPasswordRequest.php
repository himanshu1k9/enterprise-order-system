<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\DTO\ResetPasswordData;
use App\Http\Request;
use App\Validation\Rules\MinRule;
use App\Validation\Rules\RequiredRule;
use App\Validation\Rules\StringRule;
use App\Validation\Validator;

class resetPasswordRequest
{
    private array $validated = [];
    public function __construct(private Request $request)
    {}

    public function validate(): void
    {
        $header = strtolower($this->request->header('Content-Types') ?? '');
        $data = str_starts_with($header, 'application/json') ? $this->request->json() : $this->request->all();
        $validator = new Validator($data);
        $validator->validate($this->rules());
        $this->validated = $data;
    }

    private function rules(): array
    {
        return [
            'token' => [
                new RequiredRule(),
                new StringRule()
            ],
            'newPassword' => [
                new RequiredRule(),
                new StringRule(),
                new MinRule(8)
            ],
            'confirmPassword' => [
                new RequiredRule(),
                new StringRule(),
                new MinRule(8)
            ]
        ];
    }

    public function data(): ResetPasswordData
    {
        return ResetPasswordData::fromRequest($this->request);
    }

    public function validated(): array
    {
        return $this->validated;
    }
}