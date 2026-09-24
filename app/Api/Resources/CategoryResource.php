<?php

declare(strict_types = 1);

namespace App\Api\Resources;

final class CategoryResource
{
    public static function make(array $category): array
    {
        return [
            'id' => (int) $category['id'],
            'name' => $category['name']
        ];
    }

    public static function collection(array $categories): array
    {
        return array_map([self::class, 'make'], $categories);
    }
}