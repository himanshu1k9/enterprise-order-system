<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\DTO\VerifyPasswordData;
use App\Http\Request;
use App\Validation\Rules\EmailRule;
use App\Validation\Rules\RequiredRule;
use App\Validation\Rules\StringRule;
use App\Validation\Validator;

class ForgotPasswordRequest
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
            'email' => [
                new StringRule(),
                new RequiredRule(),
                new EmailRule()
            ]
        ];
    }

    public function data(): VerifyPasswordData
    {
        return VerifyPasswordData::fromRequest($this->request);
    }

    public function validated(): array
    {
        return $this->validated;
    }
}