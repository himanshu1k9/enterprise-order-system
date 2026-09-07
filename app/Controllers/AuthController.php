<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Auth\SessionManager;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Response;
use App\Services\UserService;

class AuthController
{
    public function __construct(
        private Request $request,
        private UserService $service,
        private SessionManager $sessionManager) {}

    /**
     * Controller to handeling Register Request / Response
     *
     * @return Response
     */
    public function register(): Response
    {
        $request = new RegisterRequest($this->request);
        $request->validate();

        $data = $request->data();
        $id = $this->service->register($data->name, $data->email, $data->password);

        return ApiResponse::success('Registration Successfull', ['id' => $id], 201);
    }

    /**
     * Controller to handeling Login related Request / Response
     *
     * @return Response
     */
    public function login(): Response
    {
        $request = new LoginRequest($this->request);
        $request->validate();

        $data = $request->data();
        $user = $this->service->login($data->email, $data->password);

        $this->sessionManager->login($user['id']);

        return ApiResponse::success('Login Successful', [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
    }
}