<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Jika ingin semua user bisa mengaksesnya, kembalikan true
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
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|unique:users|max:100',
            'password' => 'required|string|confirmed|min:8',
            'username' => 'required|string|unique:users|max:100|regex:/^[a-z0-9_-]+$/',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal terdiri dari 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email harus berupa format yang valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.max' => 'Email maksimal terdiri dari 100 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'username.max' => 'Username maksimal terdiri dari 100 karakter.',
            'username.regex' => 'Username hanya boleh terdiri dari huruf kecil, angka, garis bawah, dan tanda hubung.',
        ];
    }
}
