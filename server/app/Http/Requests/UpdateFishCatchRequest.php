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
            'weight' => ['required', 'numeric', 'min:0', 'max:50', 'decimal:0,2'],
            'length' => ['required', 'numeric', 'min:0', 'max:999.9', 'decimal:0,1'],
            'catchTime' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'specieId.required' => 'A halfaj megadása kötelező.',
            'specieId.exists' => 'A megadott halfaj nem létezik.',
            'lureId.required' => 'A csali megadása kötelező.',
            'lureId.exists' => 'A megadott csali nem létezik.',
            'catchLogId.required' => 'A fogási napló megadása kötelező.',
            'catchLogId.exists' => 'A megadott fogási napló nem létezik.',
            'weight.max' => 'A súly legnagyobb értéke 50 kg lehet.',
            'weight.decimal' => 'A súly legfeljebb 2 tizedes lehet.',
            'length.max' => 'A hossz legnagyobb értéke 999.9 cm lehet.',
            'length.decimal' => 'A hossz legfeljebb 1 tizedes lehet.',
        ];
    }
}
