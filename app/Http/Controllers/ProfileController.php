<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    public function getProfile()
    {
        return new UserResource(Auth::user());
    }

    public function updateProfile(UpdateProfileRequest $request) : UserResource
    {
        $user = auth()->user();
        

        // Cek apakah user login via Google
        if ($user->isGoogleUser()) {
            // Hanya izinkan update nama saja dari request
            $user->update($request->only('name'));
        } else {
            // Jika bukan user Google, update semua yang tervalidasi
            $user->update($request->validated());
        }
        
        // Check if avatar was uploaded
        if ($request->hasFile('avatar')) {
            // If user already has an avatar, delete the old one
            if ($user->avatar_public_id) {
                // Delete old image from Cloudinary
                Cloudinary::destroy($user->avatar_public_id);
            }
            // Upload new avatar to Cloudinary
            $uploadResult = Cloudinary::upload($request->file('avatar')->getRealPath());
            
            // Get the secure URL and public ID
            $uploadedFileUrl = $uploadResult->getSecurePath();
            $publicId = $uploadResult->getPublicId();
            
            // Update user's avatar and avatar_public_id
            $user->avatar = $uploadedFileUrl;
            $user->avatar_public_id = $publicId;
            $user->save();
            
            Log::info('Avatar updated: ', ['url' => $uploadedFileUrl, 'public_id' => $publicId]);
        }
        
        return new UserResource($user);
    }

    public function updatePassword(UpdatePasswordRequest $request) : JsonResponse
    {
        $user = auth()->user();

        if ($user->isGoogleUser()) {
            return response()->json(['message' => 'User Google tidak dapat mengubah password.'], 422);
        }

        // Validasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah.'], 422);
        }

        // Update password
        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json(['message' => 'Password berhasil diperbarui.']);
    }
}
