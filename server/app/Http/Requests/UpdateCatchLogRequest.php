<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatchLogRequest extends FormRequest
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
            'locationid' => ['required', 'integer', 'exists:locations,id'],
            'fishing_start' => ['required', 'date'],
            'fishing_end' => ['required', 'date', 'after_or_equal:fishing_start'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'locationid.required' => 'A hely megadása kötelező.',
            'locationid.exists' => 'A megadott hely nem létezik.',
            'fishing_start.required' => 'A horgászat kezdete kötelező.',
            'fishing_end.required' => 'A horgászat vége kötelező.',
            'fishing_end.after_or_equal' => 'A horgászat vége nem lehet korábbi, mint a kezdete.',
        ];
    }
}
