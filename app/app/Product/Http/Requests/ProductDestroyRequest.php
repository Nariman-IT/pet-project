<?php

namespace App\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Product\Models\Product;
use Illuminate\Http\Request;

class ProductDestroyRequest extends FormRequest
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
