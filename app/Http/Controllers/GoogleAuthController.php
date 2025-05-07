<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    protected GoogleAuthService $googleAuthService;

    /**
     * Constructor with dependency injection
     *
     * @param GoogleAuthService $googleAuthService
     */
    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    /**
     * Redirect to Google OAuth page
     *
     * @return JsonResponse
     */
    public function redirectToGoogle(): JsonResponse
    {
        $url = $this->googleAuthService->redirectToGoogle();

        return response()->json([
            'status' => 'success',
            'message' => 'Google authentication URL generated',
            'data' => ['url' => $url]
        ], 200);
    }

    /**
     * Handle the callback from Google
     *
     * @return JsonResponse
     */
    public function handleGoogleCallback(): JsonResponse
    {
        try {
            $authData = $this->googleAuthService->handleGoogleCallback();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully authenticated with Google',
                'data' => $authData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User profile retrieved successfully',
            'data' => ['user' => $request->user()]
        ], 200);
    }

    /**
     * Logout and revoke token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->googleAuthService->revokeGoogleAccess($user);
        $user->tokens()->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
            'data' => null
        ], 200);
    }
}