<?php

namespace App\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{   
    use ApiValidationTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:10', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', Rule::in(['drink', 'pizza'])],
        ];
    }


    public function messages(): array
    {
        return [
            'name.string' => 'Название товара должно быть строкой',
            'name.min' => 'Название товара должно содержать минимум :min символа',
            'name.max' => 'Название товара не должно превышать :max символов',

            'description.min' => 'Описание товара должно содержать минимум :min символов',
            'description.max' => 'Описание товара не должно превышать :max символов',

            'price.numeric' => 'Цена товара должна быть числом',
            'price.min' => 'Цена товара не может быть отрицательной',

            'weight.numeric' => 'Вес товара должен быть числом',
            'weight.min' => 'Вес товара не может быть отрицательным',

            'category.string' => 'Категория товара должна быть строкой',
            'category.in' => 'Категория товара должна быть либо "Напиток" (drink), либо "Пицца" (pizza)',
        ];
    }
}
