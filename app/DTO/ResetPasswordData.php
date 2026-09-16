<?php

declare(strict_types = 1);

namespace App\DTO;

use App\Http\Request;

readonly class ResetPasswordData
{
    public function __construct(public string $token, public string $newPassword, public string $confirmNewPassword)
    {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            token: $request->input('token'),
            newPassword: $request->input('newPassword'),
            confirmNewPassword: $request->input('confirmPassword')
        );
    }
}