<?php

namespace App\Report\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'Укажите начальную дату',
            'end_date.required' => 'Укажите конечную дату',
            'start_date.before_or_equal' => 'Начальная дата не может быть позже конечной',
        ];
    }
}