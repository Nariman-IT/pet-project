<?php

namespace App\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Product\Models\Product;
use Illuminate\Http\Request;

class ProductStoreRequest extends FormRequest
{
    public function authorize(Request $request): bool
    {
        return $request->user()->can('create', Product::class);  
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
}
