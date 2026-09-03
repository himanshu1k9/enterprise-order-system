<?php

declare(strict_types = 1);

namespace App\DTO;

readonly class PaginationData
{
    public function __construct(public int $page = 1, public int $limit = 20)
    {}

    public function offset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}