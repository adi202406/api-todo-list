<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Find or create user from Google data
     *
     * @param array $googleData
     * @return User
     */
    public function findOrCreateFromGoogle(array $googleData): User
    {
        $user = User::where('email', $googleData['email'])->first();
        
        if (!$user) {
            return $this->createFromGoogle($googleData);
        }
        
        return $this->updateGoogleData($user, $googleData);
    }
    
    /**
     * Create a new user from Google data
     *
     * @param array $googleData
     * @return User
     */
    public function createFromGoogle(array $googleData): User
    {
        return User::create([
            'name' => $googleData['name'],
            'email' => $googleData['email'],
            'password' => Hash::make(Str::random(16)),
            'google_id' => $googleData['id'],
            'google_token' => $googleData['token'],
            'google_refresh_token' => $googleData['refresh_token'] ?? null,
            'avatar' => $googleData['avatar'],
        ]);
    }
    
    /**
     * Update existing user with Google data
     *
     * @param User $user
     * @param array $googleData
     * @return User
     */
    public function updateGoogleData(User $user, array $googleData): User
    {
        $user->update([
            'google_id' => $googleData['id'],
            'google_token' => $googleData['token'],
            'google_refresh_token' => $googleData['refresh_token'] ?? null,
            'avatar' => $googleData['avatar'],
        ]);
        
        return $user;
    }
}