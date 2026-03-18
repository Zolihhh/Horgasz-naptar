<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecieRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'fish_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('species', 'fish_name')->ignore($id),
            ],
            'photo' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fish_name.required' => 'A halfaj neve kötelező.',
            'fish_name.string' => 'A halfaj neve csak szöveg lehet.',
            'fish_name.min' => 'A halfaj neve legalább 2 karakter legyen.',
            'fish_name.max' => 'A halfaj neve legfeljebb 100 karakter lehet.',
            'fish_name.unique' => 'Ez a halfaj már létezik.',
            'photo.required' => 'A kép megadása kötelező.',
            'photo.string' => 'A kép neve csak szöveg lehet.',
            'photo.max' => 'A kép neve legfeljebb 255 karakter lehet.',
            'description.string' => 'A leírás csak szöveg lehet.',
            'description.max' => 'A leírás legfeljebb 255 karakter lehet.',
        ];
    }
}
