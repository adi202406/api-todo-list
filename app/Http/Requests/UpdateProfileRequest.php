<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:100',
            'username' => 'sometimes|string|max:100|alpha_dash|unique:users,username,' . auth()->id(),
            'email' => 'sometimes|string|email|max:100|unique:users,email,' . auth()->id(),
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.alpha_dash' => 'Username dapat berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique' => 'Username sudah digunakan.',
            'username.max' => 'Username tidak boleh lebih dari 50 karakter.',
            'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg',
            'avatar.max' => 'The avatar may not be greater than 2MB.',
        ];
    }
}