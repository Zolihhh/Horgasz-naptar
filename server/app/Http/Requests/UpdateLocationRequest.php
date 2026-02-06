<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
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
            'waterAreaCode' => ['required','string','max:20',],
            'latitude' => ['nullable','numeric','between:-90,90',],
            'longitude' => ['nullable','numeric','between:-180,180',],
            'FishingLakeName' => ['required','string','max:80',
                // egyedi, de az aktuális rekordot hagyja
                Rule::unique('locations', 'FishingLakeName')->ignore($id),
            ],
        ];
    }

    /**
     * Egyedi hibaüzenetek
     */
    public function messages(): array
    {
        return [
            'waterAreaCode.required' => 'A vízterület kód megadása kötelező!',
            'waterAreaCode.string' => 'A vízterület kód szöveg kell legyen!',
            'waterAreaCode.max' => 'A vízterület kód maximum 20 karakter lehet!',
            'latitude.numeric' => 'A szélességi fok szám kell legyen!',
            'latitude.between' => 'A szélességi fok -90 és 90 között lehet!',
            'longitude.numeric' => 'A hosszúsági fok szám kell legyen!',
            'longitude.between' => 'A hosszúsági fok -180 és 180 között lehet!',
            'FishingLakeName.required' => 'A horgásztó neve kötelező!',
            'FishingLakeName.string' => 'A horgásztó neve szöveg kell legyen!',
            'FishingLakeName.max' => 'A horgásztó neve maximum 80 karakter lehet!',
            'FishingLakeName.unique' => 'Már létezik ilyen nevű horgásztó!',
        ];
    }
}
