<?php

namespace App\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    use ApiValidationTrait;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:10', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'category' => ['required', 'string', Rule::in(['drink', 'pizza'])],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Название товара обязательно для заполнения',
            'name.string' => 'Название товара должно быть строкой',
            'name.min' => 'Название товара должно содержать минимум :min символа',
            'name.max' => 'Название товара не должно превышать :max символов',

            'description.string' => 'Описание товара должно быть строкой',
            'description.min' => 'Описание товара должно содержать минимум :min символов',
            'description.max' => 'Описание товара не должно превышать :max символов',

            'price.required' => 'Цена товара обязательна для заполнения',
            'price.numeric' => 'Цена товара должна быть числом',
            'price.min' => 'Цена товара не может быть отрицательной',

            'weight.numeric' => 'Вес товара должен быть числом',
            'weight.min' => 'Вес товара не может быть отрицательным',

            'category.required' => 'Категория товара обязательна для выбора',
            'category.string' => 'Категория товара должна быть строкой',
            'category.in' => 'Категория товара должна быть либо "Напиток" (drink), либо "Пицца" (pizza)',
        ];
    }
}
