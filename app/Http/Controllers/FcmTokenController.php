<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmTokenController extends Controller
{
    /**
     * Save FCM token for authenticated user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Dapatkan user dari token Sanctum
        $user = $request->user();

        // Update or create device token
        $userDevice = UserDevice::updateOrCreate(
            ['device_token' => $request->device_token],
            [
                'user_id' => $user->id,
                'device_type' => $request->device_type,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token saved successfully',
            'data' => $userDevice
        ], 201);
    }

    /**
     * Send test notification to the device
     */
    public function sendTestNotification(Request $request)
    {
        $user = $request->user();
        $userDevice = UserDevice::where('user_id', $user->id)->first();

        if (!$userDevice) {
            return response()->json([
                'success' => false,
                'message' => 'No device token found for this user'
            ], 404);
        }

        try {
            $messaging = app('firebase.messaging');
            
            $notification = Notification::create('Test Notification', 'This is a test notification from Laravel');
            
            $message = CloudMessage::withTarget('token', $userDevice->device_token)
                ->withNotification($notification)
                ->withData(['type' => 'test', 'click_action' => 'FLUTTER_NOTIFICATION_CLICK']);
            
            $messaging->send($message);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove FCM token
     */
    public function destroy(Request $request, $deviceToken)
    {
        $user = $request->user();
        $deleted = UserDevice::where('user_id', $user->id)
            ->where('device_token', $deviceToken)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Device token removed successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Device token not found'
        ], 404);
    }
}