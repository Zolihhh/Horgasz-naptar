<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLureRequest extends FormRequest
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
            'lure' => ['required', 'string', 'min:2', 'max:75', 'unique:lures,lure'],
        ];
    }

    public function messages(): array
    {
        return [
            'lure.required' => 'Töltsd ki a csali nevét.',
            'lure.string' => 'A csali neve csak szöveg lehet.',
            'lure.min' => 'A csali neve legalább 2 karakter legyen.',
            'lure.max' => 'A csali neve legfeljebb 75 karakter lehet.',
            'lure.unique' => 'Ez a csali már létezik.',
        ];
    }
}
