<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Auth\JwtAuthenticatedUserResolver;
use App\Auth\JwtCurrentUser;
use App\Auth\JwtManager;
use App\Http\Request;
use App\Http\Response;
use Closure;
use RuntimeException;

class JwtAuthMiddleware
{
    // private JwtManager $jwt;
    // private JwtCurrentUser $user;
    public function __construct(private JwtManager $jwt, private JwtCurrentUser $user, private JwtAuthenticatedUserResolver $resolver)
    {
        // $this->jwt = new JwtManager($_ENV['JWT_SECRET']);
        // $this->user = new JwtCurrentUser();
    }

    /**
     *
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $token = $this->extractToken();
            $payload = $this->jwt->verifyToken($token);
            $userId = $payload['sub'];
            $authenticateUser = $this->resolver->resolve($userId);
        } catch(RuntimeException $e) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        $request->setAttribute('auth_user_id', $authenticateUser['id']);
        $request->setAttribute('auth_user', $authenticateUser);
        $this->user->set($authenticateUser);
        return $next($request);
    }

    /**
     *
     *
     * @return string
     */
    private function extractToken(): string
    {
        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if ($authorization === '') {
            throw new RuntimeException('Authorization header is required.');
        }

        $parts = preg_split('/\s+/', trim($authorization));

        if ($parts === false || count($parts) !== 2) {
            throw new RuntimeException('Invalid authorization format.');
        }

        [$scheme, $token] = $parts;

        if (strcasecmp($scheme, 'Bearer') !== 0) {
            throw new RuntimeException('Invalid authorization scheme.');
        }

        if ($token === '') {
            throw new RuntimeException('Bearer token is required.');
        }

        return $token;
    }
}