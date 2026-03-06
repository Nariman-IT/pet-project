<?php

namespace App\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Product\Http\Requests\ApiValidationTrait;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ];
    }
}
