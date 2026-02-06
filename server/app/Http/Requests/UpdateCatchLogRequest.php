<?php

namespace App\Http\Requests;

use App\Models\CatchLog;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCatchLogRequest extends FormRequest
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
            'locationId' => [
                'required',
                'integer',
                'exists:locations,id'
            ],

            'lureId' => [
                'required',
                'integer',
                'exists:lures,id'
            ],

            'userId' => [
                'required',
                'integer',
                'exist:users,id'
            ],
        
            'comment' => [
                'required',
                'string',
                'min1',
                'max250'
            ],
        ];
    }
    
        public function messages(): array
    {
        return [
            'locationId.required' => 'A hely megadása kötelező!',
            'lureId.required' => 'A csali megadása kötelező!',
            'userId.required' => 'A horgász megadása kötelező!',
            'comment.required' => 'A megjegyzés megadása kötelező!',
        ];
    }
}
