<?php

namespace App\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_address_region' => ['required', 'string', 'max:255'],
            'delivery_address_city' => ['required', 'string', 'max:255'],
            'delivery_address_street' => ['required', 'string', 'max:255'],
            'delivery_address_house' => ['required', 'string', 'max:255'],
            'delivery_address_entrance' => ['nullable', 'string', 'max:255'],
            'delivery_address_apartment' => ['nullable', 'string', 'max:255'],
        ];
    }
}
