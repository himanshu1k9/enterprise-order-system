<?php

declare(strict_types = 1);

namespace App\Http\Requests;

use App\DTO\CreateProductData;
use App\Http\Request;
use App\Validation\Rules\IntegerRule;
use App\Validation\Rules\NumericRule;
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
                new StringRule()
            ],
            'price' => [
                new RequiredRule(),
                new NumericRule()
            ],
            'stock' => [
                new RequiredRule(),
                new IntegerRule()
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