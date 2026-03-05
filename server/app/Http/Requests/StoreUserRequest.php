<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'nullable|integer|in:1,2',
            'idNumber' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:70',
            'houseNumber' => 'nullable|string|max:10',
            'postCode' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom validation messages for the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A név megadása kötelező.',
            'name.min' => 'A név legalább 2 karakter legyen.',
            'email.required' => 'Az email cím megadása kötelező.',
            'email.email' => 'Érvénytelen email cím.',
            'email.unique' => 'Ez az email cím már foglalt.',
            'password.required' => 'A jelszó megadása kötelező.',
            'password.min' => 'A jelszó legalább 4 karakter legyen.',
        ];
    }
}
