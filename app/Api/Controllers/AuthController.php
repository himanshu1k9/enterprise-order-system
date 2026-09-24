<?php

declare(strict_types = 1);

namespace App\Api\Controllers;

use App\Auth\JwtAuthenticatedUserResolver;
use App\Auth\JwtCurrentUser;
use App\Auth\JwtManager;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Response;
use App\Services\AuthService;

class AuthController
{
    public function __construct(
        private Request $request,
        private AuthService $authService,
        private JwtManager $jwtManager,
        private JwtCurrentUser $jwtUser,
        private JwtAuthenticatedUserResolver $userResolver
    ) {}

    public function login(): Response
    {
        $request = new LoginRequest($this->request);
        $request->validate();

        $data = $request->data();
        $user = $this->authService->login($data->email, $data->password);

        $token = $this->jwtManager->createToken((int) $user['id']);

        return ApiResponse::success('API login successful', [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ], 200);
    }

    public function me(): Response
    {
        $userId = $this->jwtUser->id();
        $user = $this->userResolver->resolve($userId);

        return ApiResponse::success('Authenticated user',[
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }
}