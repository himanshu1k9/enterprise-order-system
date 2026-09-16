<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Http\Request;

readonly class VerifyPasswordData
{
    public function __construct(public string $email)
    {}

    public static function fromRequest(Request $request): self
    {
        return new self(email: $request->input('email'));
    }
}