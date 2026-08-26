<?php

declare(strict_types = 1);

require_once __DIR__ .'/../vendor/autoload.php';

use ReflectionClass;
use App\Controllers\ProductController;

$reflection = new ReflectionClass(ProductController::class);
$constructor = $reflection->getConstructor();

foreach ($constructor->getParameters() as $parameter) {
    echo $parameter->getName() . "";
    echo '<br>';

    $type = $parameter->getType();
    if($type != null) {
        echo $type;
    }
    echo '<br>';
}