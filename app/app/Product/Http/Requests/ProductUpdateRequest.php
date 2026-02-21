<?php

declare(strict_types=1);

namespace App\Product\Http\Requests;

use App\Product\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class ProductUpdateRequest extends FormRequest
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
            'category' => ['nullable', 'string', Rule::in(values: ['drink', 'pizza'])],
        ];
    }
}
