<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFishCatchRequest extends FormRequest
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
            'catchLogId' => ['required', 'integer', 'exists:catch_logs,id'],
            'specieId' => ['required', 'integer', 'exists:species,id'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,2'],
            'length' => ['required', 'numeric', 'min:0', 'max:999.9', 'decimal:0,1'],
            'lureId' => ['required', 'integer', 'exists:lures,id'],
            'catchTime' => ['required', 'date'],
        ];
    }
}
