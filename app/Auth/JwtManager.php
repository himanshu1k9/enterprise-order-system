<?php

declare(strict_types = 1);

namespace App\Auth;

use RuntimeException;

class JwtManager
{
    public function __construct(private string $secret)
    {}

    /**
     * Base64 URL Encode
     *
     * @param string $data
     * @return string
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL Decode
     *
     * @param string $data
     * @return string
     */
    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if($remainder !== 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        if ($decoded === false) {
            throw new RuntimeException('Invalid Base64URL data.');
        }

        return $decoded;
    }

    /**
     * Create JWT Header
     *
     * @return string
     */
    private function createHeader(): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $json = json_encode($header, JSON_UNESCAPED_SLASHES);
        if($json === false) {
            throw new RuntimeException('Unable to encode JWT header.');
        }

        return $this->base64UrlEncode($json);
    }

    /**
    * Create JWT Payload
    *
    * @param integer $userId
    * @return string
    */
    private function createPayload(int $userId): string
    {
        $now = time();
        $payload = [
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + 3600,
            'iss' => 'enterprise-order-system', // issuer
            'aud' => 'enterprise-order-api', // consumer
            'jti' => bin2hex(random_bytes(16)) // unique jwt id
        ];

        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if($json === false) {
            throw new RuntimeException('Unable to encode JWT payload.');
        }

        return $this->base64UrlEncode($json);
    }

    /**
     * Create JWT Signature
     *
     * @param string $header
     * @param string $payload
     * @return string
     */
    private function createSignature(string $header, string $payload): string
    {
        $data = $header . '.' . $payload;
        $signature = hash_hmac('sha256', $data, $this->secret, true);
        return $this->base64UrlEncode($signature);
    }

    /**
     * Create JWT Token
     *
     * @param integer $userId
     * @return string
     */
    public function createToken(int $userId): string
    {
        $header = $this->createHeader();
        $payload = $this->createPayload($userId);
        $signatrure = $this->createSignature($header, $payload);
        return $header . '.' . $payload . '.' . $signatrure;
    }

    /**
     * Verify JWT Token
     *
     * @param string $token
     * @return array
     */
    public function verifyToken(string $token): array
    {
        $parts = explode(".", $token);
        if(count($parts) !== 3) {
            throw new RuntimeException('Invalid Jwt structure.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $headerJson = $this->base64UrlDecode($encodedHeader);
        $payloadJson = $this->base64UrlDecode($encodedPayload);
        $signatureJson = $this->base64UrlDecode($encodedSignature);

        $header = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);

        if(!is_array($header)) {
            throw new RuntimeException('Invalid JWT header.');
        }

        if(!is_array($payload)) {
            throw new RuntimeException('Invalid JWT payload.');
        }

        if(($header['alg'] ?? null) !== 'HS256') {
            throw new RuntimeException('Unsupported JWT algorithm.');
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $encodedHeader . '.' . $encodedPayload,
            $this->secret,
            true
        );

        if(!hash_equals($expectedSignature, $signatureJson)) {
            throw new RuntimeException('Invalid JWT signature.');
        }

        if(!isset($payload['exp']) || !is_int($payload['exp'])) {
            throw new RuntimeException('Invalid JWT expiration.');
        }

        if($payload['exp'] < time()) {
            throw new RuntimeException('JWT has expired.');
        }

        if(!isset($payload['sub']) || !is_int($payload['sub'])) {
            throw new RuntimeException('Invalid JWT subject.');
        }

        if(!isset($payload['iss']) || !is_string($payload['iss']) || $payload['iss'] !== 'enterprise-order-system') {
            throw new RuntimeException('Invalid JWT issuer.');
        }

        if (!isset($payload['aud']) || !is_string($payload['aud']) || $payload['aud'] !== 'enterprise-order-api') {
            throw new RuntimeException('Invalid JWT audience.');
        }

        if (!isset($payload['jti']) || !is_string($payload['jti']) || $payload['jti'] === '') {
            throw new RuntimeException('Invalid JWT ID.');
        }

        if (!isset($payload['iat']) || !is_int($payload['iat'])) {
            throw new RuntimeException('Invalid JWT issued-at time.');
        }

        return $payload;
    }
}