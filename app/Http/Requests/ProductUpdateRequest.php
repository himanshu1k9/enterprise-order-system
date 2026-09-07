<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\DTO\UpdateProductData;
use App\Http\Request;
use App\Validation\Rules\InRule;
use App\Validation\Rules\IntegerRule;
use App\Validation\Rules\MaxRule;
use App\Validation\Rules\MinRule;
use App\Validation\Rules\RegexRule;
use App\Validation\Rules\StringRule;
use App\Validation\Validator;

class ProductUpdateRequest
{
    private array $validated = [];
    public function __construct(private Request $request)
    {}

    public function validate(): void
    {
        $contentType = strtolower($this->request->header('Content-Type') ?? '');
        $data = str_starts_with($contentType, 'application/json') ? $this->request->json() : $this->request->all();
        // var_dump($data); die;
        $validator = new Validator($data);
        $validator->validate($this->rules());
        $this->validated = $data;
    }

    private function rules(): array
    {
        return [
            'name' => [
                new StringRule(),
                new MaxRule(100)
            ],
            'sku' => [
                new StringRule(),
                new RegexRule('/^SKU-[0-9]{5}$/')
            ],
            'description' => [
                new StringRule(),
                new MaxRule(1000)
            ],
            'price' => [
                new IntegerRule(),
                new MinRule(0)
            ],
            'stock' => [
                new IntegerRule(),
                new MinRule(0)
            ],
            'status' => [
                new StringRule(),
                new InRule(['active', 'inactive'])
            ]
        ];
    }

    public function data(): UpdateProductData
    {
        return UpdateProductData::fromRequest($this->request);
    }

    public function validated(): array
    {
        return $this->validated;
    }
}