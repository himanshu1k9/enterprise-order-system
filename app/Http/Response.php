<?php

declare(strict_types = 1);

namespace App\Http;

class Response
{
    public function __construct(
        private string $body = "",
        private int $status = 200,
        private array $headers = []
        ) {}

    // public static function json(array $data, int $statusCode = 200): void {
    //     http_response_code($statusCode);
    //     header('Content-Type: application/json; charset=utf-8');
    //     echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    //     exit;
    // }

    /**
     * Method to send response to the browser
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->status);
        foreach($this->headers as $name => $header) {
            header("{$name}: {$header}");
        }
        echo $this->body;
    }

    /**
     * Method to return success json data
     *
     * @param array $data
     * @param integer $status
     * @return self
     */
    public static function json(array $data, int $status = 200): self
    {
        return new self(
            body: json_encode($data, JSON_THROW_ON_ERROR),
            status: $status,
            headers: ["Content-Type" => "application/json"]
        );
    }

    /**
     * Method to return success text data
     *
     * @param string $message
     * @param integer $status
     * @return self
     */
    public static function text(string $message, int $status = 200): self
    {
        return new self(
            body: $message,
            status: $status,
            headers: ["Content-Type" => "text/plain"]
        );
    }

    /**
     * Method to return redirect
     *
     * @param string $url
     * @param integer $status
     * @return self
     */
    public static function redirect(string $url, int $status = 302): self
    {
        return new self(
            body: '',
            status: $status,
            headers: ["Location" => $url]
        );
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function status(): int
    {
        return $this->status;
    }
}