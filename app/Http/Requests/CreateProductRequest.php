<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\DTO\CreateProductData;
use App\Http\Request;
use App\Validation\Rules\IntegerRule;
use App\Validation\Rules\MaxRule;
use App\Validation\Rules\MinRule;
use App\Validation\Rules\NumericRule;
use App\Validation\Rules\RegexRule;
use App\Validation\Rules\RequiredRule;
use App\Validation\Rules\StringRule;
use App\Validation\Validator;

class CreateProductRequest
{
    private array $validated = [];
    public function __construct(
        private Request $request
    ) {}

    public function validate(): void
    {
        $header = $this->request->header('Content-Type');
        $data = $header === 'application/json' ? $this->request->json() : $this->request->all();
        // var_dump($data); die;
        $validator = new Validator($data);
        $validator->validate($this->rules());
        $this->validated = $data;
    }

    public function rules(): array
    {
        // return [
        //     'name' => ['required', 'string'],
        //     'price' => ['required', 'mumeric', 'min:0'],
        //     'stock' => ['required', 'numeric', 'min:0']
        // ];
        return [
            'name' => [
                new RequiredRule(),
                new StringRule(),
                new MinRule(3),
                new MaxRule(100)
            ],
            'price' => [
                new RequiredRule(),
                new NumericRule(),
                new MinRule(0)
            ],
            'stock' => [
                new RequiredRule(),
                new IntegerRule(),
                new MinRule(0)
            ],
            'sku' => [
                new RequiredRule(),
                new StringRule(),
                new RegexRule('/^SKU-[0-9]{5}$/')
            ],
            "description" => [
                new StringRule()
            ],
            "status" => [
                // new RequiredRule(),
                new StringRule(),
            ]
        ];
    }

    public function data(): CreateProductData
    {
        return CreateProductData::fromRequest($this->request);
    }

    public function validated(): array
    {
        return $this->validated;
    }
}