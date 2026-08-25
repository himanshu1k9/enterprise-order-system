<?php

declare(strict_types=1); // defining the strict type checking

use App\Controllers\HomeController;
use App\Services\SystemInfoService;

require_once __DIR__ . '/../bootstrap/app.php'; // Including bootstrap app file

// use App\Services\GreetingService;
// use App\Services\SystemInfoService;

// $service = new GreetingService();
// $systemInfoService = new SystemInfoService();

// echo $service->message() . "\n";
// echo $systemInfoService->getApplicationName()  . "\n";
// echo $systemInfoService->getEnvironment()  . "\n";


// echo "Enterprise Order Management System";
$systemInfo = new SystemInfoService();
$homeController = new HomeController($systemInfo);

$systemInfor = $homeController->index();

echo '<pre>';
print_r($systemInfor);
echo '</pre>';


