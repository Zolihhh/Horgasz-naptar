<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('fishingLakeName') && !$this->filled('FishingLakeName')) {
            $this->merge([
                'FishingLakeName' => $this->input('fishingLakeName'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'waterAreaCode' => ['required', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'FishingLakeName' => ['required', 'string', 'max:80', 'unique:locations,FishingLakeName'],
        ];
    }

    public function messages(): array
    {
        return [
            'waterAreaCode.required' => 'A vízterület kód megadása kötelező.',
            'waterAreaCode.string' => 'A vízterület kód csak szöveg lehet.',
            'waterAreaCode.max' => 'A vízterület kód legfeljebb 20 karakter lehet.',
            'latitude.numeric' => 'A szélességi fok szám legyen.',
            'latitude.between' => 'A szélességi fok -90 és 90 között lehet.',
            'longitude.numeric' => 'A hosszúsági fok szám legyen.',
            'longitude.between' => 'A hosszúsági fok -180 és 180 között lehet.',
            'FishingLakeName.required' => 'A horgásztó neve kötelező.',
            'FishingLakeName.string' => 'A horgásztó neve csak szöveg lehet.',
            'FishingLakeName.max' => 'A horgásztó neve legfeljebb 80 karakter lehet.',
            'FishingLakeName.unique' => 'Ez a horgásztó már létezik.',
        ];
    }
}
