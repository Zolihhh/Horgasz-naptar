<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatchLogRequest extends FormRequest
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
        'userid' => 'required|integer|exists:users,id',
        'locationid'    => 'required|integer|exists:locations,id',
        'comment'       => 'nullable|string',
        'fishing_start' => 'required|date',
        'fishing_end'   => 'required|date|after:fishing_start',


        'fish_catches'                 => 'required|array',
        'fish_catches.*.species_id'    => 'required|integer|exists:species,id',
        'fish_catches.*.weight'        => 'required|numeric|min:0',
        'fish_catches.*.length'        => 'required|numeric|min:0',
        'fish_catches.*.lure_id'       => 'required|integer|exists:lures,id',
        'fish_catches.*.catch_time'    => 'required|date|after_or_equal:fishing_start|before_or_equal:fishing_end',
    ];

    }
}
