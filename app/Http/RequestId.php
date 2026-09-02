<?php

declare(strict_types = 1);

namespace App\Http;

class RequestId
{
    private string $id;
    public function __construct()
    {
        $incomingId = $_SERVER['HTTP_X_REQUEST_ID'] ?? null;

        if (
            is_string($incomingId)
            && preg_match('/^[a-zA-Z0-9._-]{1,100}$/', $incomingId)
        ) {
            $this->id = $incomingId;
            return;
        }

        $this->id = bin2hex(random_bytes(16));
    }

    public function get(): string
    {
        return $this->id;
    }
}