<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\Http\Request;
use App\Validation\Rules\IntegerRule;
use App\Validation\Rules\MaxRule;
use App\Validation\Rules\MinRule;
use App\Validation\Rules\StringRule;
use App\Validation\Validator;

class ProductIndexRequest
{
    private array $errors = [];
    public function __construct(private Request $request)
    {}

    /**
     * Defining the validations rules for the product filters and query
     *
     * @return void
     */
    public function validate(): void
    {
        $data = [
            'page' => $this->request->query('page', 1),
            'limit' => $this->request->query('limit', 10),
            'status' => $this->request->query('status'),
            'search' => $this->request->query('search')
        ];

        $validator = new Validator($data);
        $validator->validate([
            'page' => [
                new IntegerRule(),
                new MinRule(1)
            ],
            'limit' => [
                new IntegerRule(),
                new MinRule(1),
                new MaxRule(100)
            ],
            'status' => [
                new StringRule(),
            ],
            'search' => [
                new StringRule(),
                new MaxRule(100)
            ]
        ]);
    }

    /**
     * Method to return page
     *
     * @return integer
     */
    public function page(): int
    {
        return (int) $this->request->query('page', 1);
    }

    /**
     * Method to return limit
     *
     * @return integer
     */
    public function limit(): int
    {
        return (int) $this->request->query('limit', 10);
    }

    /**
     * Method to return status
     *
     * @return string
     */
    public function status(): ?string
    {
        return $this->request->query('status');
    }

    /**
     * Method to return search params
     *
     * @return string|null
     */
    public function search(): ?string
    {
        $search = $this->request->query('search');
        return $search !== null ? (string) $search : null;
    }
}