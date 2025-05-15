<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationService
{
    public function sendPushNotification(User $user, string $title, string $body, array $data = [])
    {
        $devices = $user->devices()->pluck('device_token')->toArray();

        if (empty($devices)) {
            return false;
        }

        $messaging = Firebase::messaging();
        $notification = Notification::create($title, $body);

        foreach ($devices as $deviceToken) {
            try {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData($data)
                    ->withChangedTarget('token', $deviceToken);

                $messaging->send($message);
            } catch (\Exception $e) {
                report($e);
                continue;
            }
        }

        return true;
    }
}