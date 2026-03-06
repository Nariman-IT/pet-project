<?php

declare(strict_types=1);

namespace App\Product\Http\Requests;

use App\Product\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

final class ProductDestroyRequest extends FormRequest
{
    public function authorize(Request $request): bool
    {
        return $request->user()->can('delete', Product::class);
    }

    public function rules(): array
    {
        return [];
    }
}
