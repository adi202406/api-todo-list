<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendWorkspaceInvitationNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private User $user,
        private array $notificationData
    ) {}

    public function handle()
    {
        $messaging = app('firebase.messaging');
        
        $deviceTokens = $this->user->devices()
            ->pluck('device_token')
            ->toArray();
        
        if (empty($deviceTokens)) {
            return;
        }

        $notification = Notification::create(
            $this->notificationData['title'],
            $this->notificationData['body']
        );

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData([
                'type' => $this->notificationData['type'],
                'workspace_id' => $this->notificationData['workspace_id'],
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]);

        $messaging->sendMulticast($message, $deviceTokens);
    }
}