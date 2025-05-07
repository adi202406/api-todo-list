<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthService
{
    protected UserRepository $userRepository;
    
    /**
     * Constructor with dependency injection
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    /**
     * Redirect the user to Google authentication page
     *
     * @return string
     */
    public function redirectToGoogle(): string
    {
        return Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Handle Google callback and authenticate user
     *
     * @return array
     */
    public function handleGoogleCallback(): array
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Prepare Google data for repository
            $googleData = [
                'id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->getAvatar(),
            ];
            
            // Find or create user through repository
            $user = $this->userRepository->findOrCreateFromGoogle($googleData);
            
            // Create API token
            $token = $user->createToken('google-auth-token')->plainTextToken;
            
            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ];
        } catch (Exception $e) {
            throw new Exception('Google authentication failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Revoke user's Google access and refresh tokens
     *
     * @param User $user
     * @return bool
     */
    public function revokeGoogleAccess(User $user): bool
    {
        if ($user->google_token) {
            $user->update([
                'google_token' => null,
                'google_refresh_token' => null,
            ]);
            return true;
        }
        
        return false;
    }
}