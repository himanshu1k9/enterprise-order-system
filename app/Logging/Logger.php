<?php

declare(strict_types = 1);

namespace App\Logging;

use App\Http\RequestId;

class Logger
{
    public function __construct(private string $file, private RequestId $requestId)
    {}

    public function debug(string $message, array $content = []): void
    {
        $this->write('DEBUG', $message, $content);
    }

    public function info(string $message, array $content = []): void
    {
        $this->write('INFO', $message, $content);
    }

    public function warning(string $message, array $content = []): void
    {
        $this->write('WARNING', $message, $content);
    }

    public function error(string $message, array $content = []): void
    {
        $this->write('ERROR', $message, $content);
    }

    public function write(string $level, string $message, array $content): void
    {
        $entry = [
            'timestamp' => date(DATE_ATOM),
            'level' => $level,
            'message' => $message,
            'content' => $content
        ];

        $line = json_encode(
            $entry,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) . PHP_EOL;

        $directory = dirname($this->file);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $result = file_put_contents(
            $this->file,
            $line,
            FILE_APPEND | LOCK_EX
        );
            // var_dump($result); die;
        if ($result === false) {
            throw new \RuntimeException(
                'Unable to write log file: ' . $this->file
            );
        }
    }
}