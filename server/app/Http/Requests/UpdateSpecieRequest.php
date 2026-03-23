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
            'photo' => ['nullable', 'file', 'image', 'max:5120'],
            'existing_photo' => ['nullable', 'string', 'max:255'],
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
            'photo.file' => 'A kép feltöltése nem sikerült.',
            'photo.image' => 'Csak képfájlt tölthetsz fel.',
            'photo.max' => 'A kép legfeljebb 5 MB lehet.',
            'existing_photo.string' => 'A meglévő kép neve hibás.',
            'existing_photo.max' => 'A meglévő kép neve túl hosszú.',
            'description.string' => 'A leírás csak szöveg lehet.',
            'description.max' => 'A leírás legfeljebb 255 karakter lehet.',
        ];
    }
}
