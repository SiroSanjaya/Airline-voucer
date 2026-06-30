<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flightNumber' => ['required', 'string', 'max:20'],
            'date'         => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'flightNumber.required' => 'Flight number is required.',
            'flightNumber.string'   => 'Flight number must be a string.',
            'date.required'         => 'Date is required.',
            'date.date_format'      => 'Date must be in YYYY-MM-DD format.',
        ];
    }
}
