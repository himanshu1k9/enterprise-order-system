<?php

declare(strict_types = 1);

setcookie(
    'username', 'himanshu', // time() + 3600, '/'
    [
        'expires' => time() + 3600,
        'path' => '/',
        'secure' => false, // make true on server
        'httponly' => true,
        'samesite' => 'lax'
    ]
);

echo 'Cookie has been set.';