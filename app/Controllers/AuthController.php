<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Response;
use App\Services\UserService;

class AuthController
{
    public function __construct(private Request $request, private UserService $service)
    {}

    public function register(): Response
    {
        $request = new RegisterRequest($this->request);
        $request->validate();

        $data = $request->data();
        $id = $this->service->register($data->name, $data->email, $data->password);

        return ApiResponse::success('Registration Successfull', ['id' => $id], 201);
    }
}