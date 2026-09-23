<?php

$secret = $_ENV['JWT_SECRET'] ?? '';
if($secret === '') {
    throw new RuntimeException('Jwt secret not provided.');
}

return [
    'jwt' => [
        'secret' => $secret
    ]
];