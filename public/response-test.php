<?php

declare(strict_types = 1);

require_once __DIR__ . '/../app/Http/Response.php';  

use App\Http\Response;

Response::json([
    'success' => true,
    'message' => 'Fetched data successfully',
    'data' => [
        'id' => 25,
        'name' => 'Laptop'
    ]
], 200);