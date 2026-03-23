<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFishCatchRequest extends FormRequest
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
            'specieId' => ['required', 'integer', 'exists:species,id'],
            'lureId' => ['required', 'integer', 'exists:lures,id'],
            'catchLogId' => ['required', 'integer', 'exists:catch_logs,id'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,2'],
            'length' => ['required', 'numeric', 'min:0', 'max:200', 'decimal:0,1'],
            'catchTime' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'catchLogId.required' => 'Válassz fogási naplót.',
            'catchLogId.integer' => 'A fogási napló azonosítója érvénytelen.',
            'catchLogId.exists' => 'A kiválasztott fogási napló nem létezik.',
            'specieId.required' => 'Válassz halfajt.',
            'specieId.integer' => 'A halfaj azonosítója érvénytelen.',
            'specieId.exists' => 'A kiválasztott halfaj nem létezik.',
            'lureId.required' => 'Válassz csalit.',
            'lureId.integer' => 'A csali azonosítója érvénytelen.',
            'lureId.exists' => 'A kiválasztott csali nem létezik.',
            'weight.required' => 'Töltsd ki a súlyt.',
            'weight.numeric' => 'A súly csak szám lehet.',
            'weight.min' => 'A súly nem lehet negatív.',
            'weight.max' => 'A súly legnagyobb értéke 100 kg lehet.',
            'weight.decimal' => 'A súly legfeljebb 2 tizedesjegyet tartalmazhat.',
            'length.required' => 'Töltsd ki a hosszt.',
            'length.numeric' => 'A hossz csak szám lehet.',
            'length.min' => 'A hossz nem lehet negatív.',
            'length.max' => 'A hossz legnagyobb értéke 200 cm lehet.',
            'length.decimal' => 'A hossz legfeljebb 1 tizedesjegyet tartalmazhat.',
            'catchTime.required' => 'Töltsd ki a fogás időpontját.',
            'catchTime.date' => 'A fogás időpontja nem megfelelő formátumú.',
        ];
    }
}
