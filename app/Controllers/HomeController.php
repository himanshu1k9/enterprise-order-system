<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Http\Response;
use App\Services\SystemInfoService;

class HomeController
{
    protected SystemInfoService $systemInfo;
    public function __construct(SystemInfoService $systemInfo)
    {
        $this->systemInfo = $systemInfo;
    }

    // public function index(): array {
    //     $appName =  $this->systemInfo->getApplicationName();
    //     $environment = $this->systemInfo->getEnvironment();

    //     return ['App Name' => $appName, 'Environment' => $environment];
    // }

    public function index(): Response {
        return Response::json(
            [
                'success' => true,
                'message' => 'Welcome to Enterprise Order Management System'
            ]
        );
    }
}