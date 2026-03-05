<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = (int) $this->route('id');

        return [
            'name' => 'nullable|string|min:2',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => 'nullable|string|min:4',
            'role' => 'nullable|integer|in:1,2',
            'idNumber' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:70',
            'houseNumber' => 'nullable|string|max:10',
            'postCode' => 'nullable|string|max:10',
        ];
    }
}
