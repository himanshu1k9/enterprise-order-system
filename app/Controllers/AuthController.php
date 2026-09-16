<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Auth\SessionManager;
use App\Http\ApiResponse;
use App\Http\Request;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\resetPasswordRequest;
use App\Http\Response;
use App\Security\CsrfManager;
use App\Services\AuthService;
use App\Services\PasswordResetService;
use App\Services\UserService;

class AuthController
{
    public function __construct(
        private Request $request,
        private UserService $service,
        private AuthService $authService,
        private SessionManager $sessionManager,
        private PasswordResetService $passwordResetService,
        private CsrfManager $csrf
        ) {}

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
        $user = $this->authService->login($data->email, $data->password);
        $sessionVersion = $this->authService->getSessionVersion($user['id']);
        $this->sessionManager->login($user['id'], $sessionVersion);

        return ApiResponse::success('Login Successful', [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
    }

    /**
     * Endpoint to logout the user
     *
     * @return Response
     */
    public function logout(): Response
    {
        $this->sessionManager->logout();
        return ApiResponse::success('Logged out successfully.');
    }

    /**
     * Endpoint to send CSRF
     *
     * @return Response
     */
    public function csrf(): Response
    {
        return ApiResponse::success('CSRF token generated', [
            'token' => $this->csrf->token()
        ]);
    }

    /**
     * Method to forgot Password
     *
     * @return Response
     */
    public function forgotPassword(): Response
    {
        $request = new ForgotPasswordRequest($this->request);
        $request->validate();

        $data = $request->data();
        $email = $data->email;

        $token = $this->passwordResetService->createResetToken($email);
        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        |
        | Do NOT reveal whether the email exists.
        |
        */
        $resData = [];
        $resMessage = 'If the account exists, a password reset link has been sent.';
        /*
        |--------------------------------------------------------------------------
        | DEVELOPMENT ONLY
        |--------------------------------------------------------------------------
        |
        | We temporarily expose the token so we can test
        | the reset flow before email integration.
        |
        | REMOVE THIS BEFORE PRODUCTION.
        |--------------------------------------------------------------------------
        */
        if($token !== false) {
            $resData['debug_token'] = $token;
        }

        return ApiResponse::success($resMessage, $resData);
    }

    /**
     * Method to reset the password
     *
     * @return Response
     */
    public function resetPassword(): Response
    {
        $request = new resetPasswordRequest($this->request);
        $request->validate();

        $data = $request->data();
        $token = $data->token;
        $newPassword = $data->newPassword;
        $confirmPassword = $data->confirmNewPassword;

        $res = $this->passwordResetService->passwordReset($token, $newPassword, $confirmPassword);
        if(!$res) {
            return ApiResponse::error('Invalid or expired password reset token.', 400);
        }

        return ApiResponse::success('Password has been reset successfully.');
    }
}