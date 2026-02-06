<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFishCatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // frissítés engedélyezve
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
            'specieId' => ['required','integer','exists:species,id', ],
            'lureId' => ['required','integer','exists:lures,id',],
            'catchLogId' => [  'required','integer','exists:catch_logs,id',],
            'weight' => ['required','numeric','min:0',],
            'length' => ['required','numeric','min:0',],
            'catchTime' => ['required','date',],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'specieId.required' => 'A halfaj megadása kötelező!',
            'specieId.integer' => 'A halfaj azonosító szám kell legyen!',
            'specieId.exists' => 'A megadott halfaj nem létezik!',
            'lureId.required' => 'A csali megadása kötelező!',
            'lureId.integer' => 'A csali azonosító szám kell legyen!',
            'lureId.exists' => 'A megadott csali nem létezik!',
            'catchLogId.required' => 'A fogási napló megadása kötelező!',
            'catchLogId.integer' => 'A fogási napló azonosító szám kell legyen!',
            'catchLogId.exists' => 'A megadott fogási napló nem létezik!',
            'weight.required' => 'A súly megadása kötelező!',
            'weight.numeric' => 'A súly szám kell legyen!',
            'weight.min' => 'A súly nem lehet negatív!',
            'length.required' => 'A hossz megadása kötelező!',
            'length.numeric' => 'A hossz szám kell legyen!',
            'length.min' => 'A hossz nem lehet negatív!',
            'catchTime.required' => 'A fogás ideje kötelező!',
            'catchTime.date' => 'A fogás ideje érvényes dátum kell legyen!',
        ];
    }
}
