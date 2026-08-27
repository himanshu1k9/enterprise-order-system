<?php

declare(strict_types = 1);

namespace App\Http;

class Request 
{
    // returns method name
    public function method(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    // returns requested url
    public function url(): string {
        return parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );
    }

    // returns json body
    public function json(): array  {
        $body = file_get_contents('php://input');

        return json_decode(
            $body,
            true
        ) ?? [];
    }

    // retrns all queries and params
    public function query(): array {
        return $_GET;
    }
}