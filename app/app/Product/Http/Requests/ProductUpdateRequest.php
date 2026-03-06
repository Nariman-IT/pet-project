<?php

namespace App\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Product\Models\Product;
use Illuminate\Http\Request;

class ProductUpdateRequest extends FormRequest
{   
    public function authorize(Request $request): bool
    {
        return $request->user()->can('update', Product::class);
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
}
