<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Mivel a bejelentkezett user a saját jelszavát módosítja, ez true
        return true; 
    }

    public function rules(): array
    {
        return [
            // Ellenőrzi, hogy a megadott 'oldpassword' azonos-e a DB-ben lévővel
            'oldpassword' => ['required', 'current_password'], 
            'newpassword' => ['required', 'string', Password::min(3), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'oldpassword.required' => 'A jelenlegi jelszó megadása kötelező.',
            'oldpassword.current_password' => 'A jelenlegi jelszó hibás.',
            'newpassword.required' => 'Az új jelszó megadása kötelező.',
            'newpassword.string' => 'Az új jelszó szöveg kell legyen.',
            'newpassword.min' => 'Az új jelszó legalább 3 karakter legyen.',
            'newpassword.confirmed' => 'Az új jelszó megerősítése nem egyezik.',
        ];
    }

    public function attributes(): array
    {
        return [
            'oldpassword' => 'jelenlegi jelszó',
            'newpassword' => 'új jelszó',
            'newpassword_confirmation' => 'új jelszó megerősítése',
        ];
    }
}
