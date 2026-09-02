<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\RequestId;
use App\Http\Response;
use App\Logging\Logger;
use Override;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private Logger $logger, private RequestId $requestId)
    {}

    #[Override]
    public function handle(Request $request, callable $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = (microtime(true) - $start) * 1000;
        $this->logger->info(
            'Request Completed',
            [
                'request_id' => $this->requestId->get(),
                'method' => $request->method(),
                'url' => $request->url(),
                'status' => $response->status(),
                'duration_ms' => round($duration, 2),
            ]
        );
        return $response;
    }
}