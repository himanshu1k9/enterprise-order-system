<?php

declare(strict_types = 1);

namespace App\Http;

class Request
{
    /**
     * Method to return method name
     *
     * @return string
     */
    public function method(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Method to return url
     *
     * @return string
     */
    public function url(): string {
        return parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        ) ?: '/';
    }

    /**
     * Method to return query
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {
        if($key === null)
            return $_GET;

        return $_GET[$key] ?? $default;
    }

    /**
     * Method to return input value(s)
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function input(?string $key = null, mixed $default = null): mixed
    {
        $data = $this->all();
        if($key === null)
            return $data;
        return $data[$key] ?? $default;
    }

    /**
     * Method to returm all inputs
     *
     * @return array
     */
    public function all(): array
    {
        return array_merge($_POST, $this->json());
    }

    /**
     * Method to return json values
     *
     * @return array
     */
    public function json(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        if(!str_contains(strtolower($contentType), 'application/json')) {
            return [];
        }
        
        $body = file_get_contents('php://input');
        if($body === false || $body === '') {
            return [];
        }

        // return json_decode(
        //     $body,
        //     true
        // ) ?? [];
        $data = json_decode($body, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Method to return header
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function header(string $key, mixed $default = null):mixed
    {
        $headers = getallheaders();
        foreach($headers as $name => $header) {
            if(strtolower($name) === strtolower($key)) {
                return $header;
            }
        }
        return $default;
    }

    /**
     * Method to return cookies
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $_COOKIE[$key] ?? $default;
    }

    /**
     * Method to return file
     *
     * @param string $key
     * @return mixed
     */
    public function file(string $key): mixed
    {
        return $_FILES[$key] ?? null;
    }
}