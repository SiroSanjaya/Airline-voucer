<?php

namespace App\Http\Requests;

use App\Services\SeatGeneratorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'id'           => ['required', 'string', 'max:50'],
            'flightNumber' => ['required', 'string', 'max:20'],
            'date'         => ['required', 'date_format:Y-m-d'],
            'aircraft'     => ['required', 'string', Rule::in(SeatGeneratorService::validAircraftTypes())],
        ];
    }

    public function messages(): array
    {
        $validTypes = implode(', ', SeatGeneratorService::validAircraftTypes());

        return [
            'name.required'         => 'Crew name is required.',
            'id.required'           => 'Crew ID is required.',
            'flightNumber.required' => 'Flight number is required.',
            'date.required'         => 'Date is required.',
            'date.date_format'      => 'Date must be in YYYY-MM-DD format.',
            'aircraft.required'     => 'Aircraft type is required.',
            'aircraft.in'           => "Aircraft type must be one of: {$validTypes}.",
        ];
    }
}
