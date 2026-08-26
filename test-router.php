<?php

/**
 * This file is for just testing how routes works 
 */

declare(strict_types = 1);

/**
* Temprory route matching function
 */
function matchRoutes(string $route, string $url): array | false {
    $routeParts = explode('/', $route); // Making array of incoming Route
    $urlParts = explode('/', $url); // Making array of incoming URL

    if(count($routeParts) !== count($urlParts)) {
        return false;
    }

    $parameters = [];
    foreach($routeParts as $key => $routePart) {
        $urlPart = $urlParts[$key];

        // If matched this condition means it's dynamic data from URL
        if(str_starts_with($routePart, '{') && str_ends_with($routePart, '}')) {
            $parameterName = trim($routePart, '{}');

            $parameters[$parameterName] = $urlPart;
            continue;
        }

        // If not matched Routepart with URLpart returning false
        if($routePart !== $urlPart) {
            return false;
        }
    }

    // Returning all dynamic data array from incoming URL with names as key
    return $parameters;
}

$result = matchRoutes('/products/{id}/category/{category_id}', '/products/25/category/2');
var_dump($result);

$pattern = '#^/products/([^/]+)$#';
$url1 = '/products/25';

preg_match($pattern, $url1, $matches);
print_r($matches);

function convertRouteToRegex(
    string $route
): string {
    $pattern = preg_replace(
        '/\{([^}]+)\}/',
        '([^/]+)',
        $route
    );

    return '#^' . $pattern . '$#';
}

print_r(convertRouteToRegex('/products/25/reviews/99'));