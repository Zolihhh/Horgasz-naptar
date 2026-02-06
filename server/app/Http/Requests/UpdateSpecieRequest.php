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
            'fish_name' => ['required','string','min:2','max:100',
                // egyedi, de az aktuális rekordot hagyja
                Rule::unique('species', 'fish_name')->ignore($id),
            ],
            'photo' => ['required','string','max:255',
            ],
        ];
    }

    /**
     * Egyedi hibaüzenetek
     */
    public function messages(): array
    {
        return [
            'fish_name.required' => 'A halfaj neve kötelező!',
            'fish_name.string' => 'A halfaj neve szöveg kell legyen!',
            'fish_name.min' => 'A halfaj neve legalább 2 karakter!',
            'fish_name.max' => 'A halfaj neve maximum 100 karakter lehet!',
            'fish_name.unique' => 'Már létezik ilyen nevű halfaj!',
            'photo.required' => 'A kép megadása kötelező!',
            'photo.string' => 'A kép elérési útja szöveg kell legyen!',
            'photo.max' => 'A kép elérési útja túl hosszú!',
        ];
    }
}
