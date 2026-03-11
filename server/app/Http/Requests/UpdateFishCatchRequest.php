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
            'length' => ['required', 'numeric', 'min:0', 'max:999.9', 'decimal:0,1'],
            'catchTime' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'specieId.required' => 'A halfaj megadĂˇsa kĂ¶telezĹ‘.',
            'specieId.exists' => 'A megadott halfaj nem lĂ©tezik.',
            'lureId.required' => 'A csali megadĂˇsa kĂ¶telezĹ‘.',
            'lureId.exists' => 'A megadott csali nem lĂ©tezik.',
            'catchLogId.required' => 'A fogĂˇsi naplĂł megadĂˇsa kĂ¶telezĹ‘.',
            'catchLogId.exists' => 'A megadott fogĂˇsi naplĂł nem lĂ©tezik.',
            'weight.max' => 'A sĂşly legnagyobb Ă©rtĂ©ke 100 kg lehet.',
            'weight.decimal' => 'A sĂşly legfeljebb 2 tizedes lehet.',
            'length.max' => 'A hossz legnagyobb Ă©rtĂ©ke 999.9 cm lehet.',
            'length.decimal' => 'A hossz legfeljebb 1 tizedes lehet.',
        ];
    }
}
