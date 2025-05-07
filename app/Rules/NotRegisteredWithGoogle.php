<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NotRegisteredWithGoogle implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(protected User $userModel)
    {
        // Constructor injection of User model
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = $this->userModel->where('email', $value)->first();
        
        if ($user && $user->google_id !== null) {
            $fail('Email sudah terdaftar dengan akun Google. Silakan login menggunakan Google.');
        } elseif ($user) {
            $fail('Email ini sudah digunakan.');
        }
    }
}