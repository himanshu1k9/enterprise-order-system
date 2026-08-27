<?php

declare(strict_types = 1);

require_once __DIR__ . '/../bootstrap/app.php';

$pdo1 = $container->get(PDO::class);
$pdo2 = $container->get(PDO::class);

var_dump($pdo1 === $pdo2);