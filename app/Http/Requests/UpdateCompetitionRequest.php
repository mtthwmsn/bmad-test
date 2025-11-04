<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'date' => 'required|date',
            'status' => 'required|in:draft,active,completed',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The competition name is required.',
            'city.required' => 'The city is required.',
            'country_code.required' => 'The country code is required.',
            'country_code.size' => 'The country code must be exactly 2 characters (ISO 3166-1 alpha-2).',
            'date.required' => 'The competition date is required.',
            'date.date' => 'The competition date must be a valid date.',
            'status.required' => 'The competition status is required.',
            'status.in' => 'The status must be one of: draft, active, or completed.',
        ];
    }
}
