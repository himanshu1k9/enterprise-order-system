<?php 

declare(strict_types = 1);

$body = file_get_contents('php://input');

$data = json_decode($body, true);

echo '<pre>';
print_r($data); 
echo '</pre>';