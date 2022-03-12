<?php

namespace App\Http\Requests\Api\V1\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'unique:users,email,'.auth()->id()],
            'password' => ['nullable', 'confirmed'],
        ];
    }
}
